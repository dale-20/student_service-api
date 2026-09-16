<?php

use App\Enums\CourseOfferingStatus;
use App\Enums\EnrollmentStatus;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a multi-offering enrollment atomically', function () {
    $registrar = User::factory()->registrar()->create();
    $student = Student::factory()->create();
    $offerings = CourseOffering::factory()->count(2)->create(['status' => CourseOfferingStatus::Open, 'capacity' => 2]);

    $this->actingAs($registrar)->postJson('/api/v1/enrollments', [
        'student_id' => $student->id,
        'course_offering_ids' => $offerings->pluck('id')->all(),
    ])->assertSuccessful()->assertJsonCount(2, 'data');

    expect(Enrollment::query()->where('student_id', $student->id)->count())->toBe(2);
});

it('rolls back the complete selection when one offering conflicts', function () {
    $registrar = User::factory()->registrar()->create();
    $student = Student::factory()->create();
    $available = CourseOffering::factory()->create(['status' => CourseOfferingStatus::Open, 'capacity' => 2]);
    $duplicate = CourseOffering::factory()->create(['status' => CourseOfferingStatus::Open, 'capacity' => 2]);
    Enrollment::factory()->create(['student_id' => $student->id, 'course_offering_id' => $duplicate->id]);

    $this->actingAs($registrar)->postJson('/api/v1/enrollments', [
        'student_id' => $student->id,
        'course_offering_ids' => [$available->id, $duplicate->id],
    ])->assertConflict();

    expect(Enrollment::query()->where('student_id', $student->id)->where('course_offering_id', $available->id)->exists())->toBeFalse();
});

it('enforces capacity and releases seats for dropped enrollments', function () {
    $registrar = User::factory()->registrar()->create();
    $offering = CourseOffering::factory()->create(['status' => CourseOfferingStatus::Open, 'capacity' => 1]);
    Enrollment::factory()->create(['course_offering_id' => $offering->id, 'status' => EnrollmentStatus::Dropped]);
    $student = Student::factory()->create();

    $this->actingAs($registrar)->postJson('/api/v1/enrollments', ['student_id' => $student->id, 'course_offering_ids' => [$offering->id]])->assertSuccessful();
});

it('allows only the assigned instructor or staff to batch update eligible grades', function () {
    $instructor = User::factory()->instructor()->create();
    $otherInstructor = User::factory()->instructor()->create();
    $offering = CourseOffering::factory()->create(['instructor_id' => $instructor->id]);
    $enrollment = Enrollment::factory()->create(['course_offering_id' => $offering->id, 'status' => EnrollmentStatus::Enrolled]);
    $payload = ['grades' => [['enrollment_id' => $enrollment->id, 'midterm_grade' => 1.75, 'final_grade' => 1.50, 'remarks' => 'passed']]];

    $this->actingAs($otherInstructor)->putJson("/api/v1/course-offerings/{$offering->id}/grades", $payload)->assertForbidden();
    $this->actingAs($instructor)->putJson("/api/v1/course-offerings/{$offering->id}/grades", $payload)->assertOk();

    expect(Grade::query()->where('enrollment_id', $enrollment->id)->count())->toBe(1);
});

it('rolls back a grade batch containing an ineligible enrollment', function () {
    $registrar = User::factory()->registrar()->create();
    $offering = CourseOffering::factory()->create();
    $eligible = Enrollment::factory()->create(['course_offering_id' => $offering->id, 'status' => EnrollmentStatus::Enrolled]);
    $dropped = Enrollment::factory()->create(['course_offering_id' => $offering->id, 'status' => EnrollmentStatus::Dropped]);

    $this->actingAs($registrar)->putJson("/api/v1/course-offerings/{$offering->id}/grades", ['grades' => [
        ['enrollment_id' => $eligible->id, 'final_grade' => 1.25, 'remarks' => 'passed'],
        ['enrollment_id' => $dropped->id, 'final_grade' => 2.00, 'remarks' => 'passed'],
    ]])->assertConflict();

    expect(Grade::query()->count())->toBe(0);
});
