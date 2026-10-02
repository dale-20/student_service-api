<?php

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Program;
use App\Models\ProgramCourse;
use App\Models\Student;
use App\Models\User;

it('preserves curriculum ids and optional flags and recovers removed entries', function () {
    $this->actingAs(User::factory()->registrar()->create());
    $entry = ProgramCourse::factory()->create(['semester' => 'first', 'is_required' => false]);
    $url = '/api/v1/programs/'.$entry->program_id;
    $this->getJson($url)->assertOk()->assertJsonPath('data.curriculum.0.id', $entry->id)->assertJsonPath('data.curriculum.0.is_required', false);
    $row = ['course_id' => $entry->course_id, 'year_level' => 2, 'semester' => 'second', 'is_required' => false];
    $this->putJson($url.'/curriculum', ['courses' => [$row]])->assertOk()->assertJsonPath('data.curriculum.0.id', $entry->id);
    $this->putJson($url.'/curriculum', ['courses' => []])->assertOk()->assertJsonCount(0, 'data.curriculum');
    $this->assertSoftDeleted($entry);
    $this->postJson('/api/v1/recycle-bin/curriculum/'.$entry->id.'/restore')->assertOk();
    $this->getJson($url)->assertOk()->assertJsonPath('data.curriculum.0.semester', 'second');
    $this->putJson($url.'/curriculum', ['courses' => []])->assertOk();
    $this->putJson($url.'/curriculum', ['courses' => [$row]])->assertOk()->assertJsonPath('data.curriculum.0.id', $entry->id);
    $this->assertDatabaseCount('program_courses', 1);
});

it('soft deletes schedules and reuses their identity when added again', function () {
    $this->actingAs(User::factory()->registrar()->create());
    $offering = CourseOffering::factory()->create();
    $url = '/api/v1/course-offerings/'.$offering->id;
    $payload = ['course_id' => $offering->course_id, 'academic_term_id' => $offering->academic_term_id, 'section' => $offering->section, 'instructor_id' => null, 'capacity' => 30, 'status' => 'open'];
    $row = ['day_of_week' => 'monday', 'start_time' => '08:00', 'end_time' => '09:00', 'room' => 'A'];
    $id = $this->putJson($url, [...$payload, 'schedules' => [$row]])->assertOk()->json('data.schedules.0.id');
    $this->putJson($url, [...$payload, 'schedules' => [$row]])->assertOk()->assertJsonPath('data.schedules.0.id', $id);
    $this->putJson($url, [...$payload, 'schedules' => []])->assertOk()->assertJsonCount(0, 'data.schedules');
    $this->assertSoftDeleted('course_offering_schedules', ['id' => $id]);
    $this->putJson($url, [...$payload, 'schedules' => [$row]])->assertOk()->assertJsonPath('data.schedules.0.id', $id);
    $this->putJson($url, [...$payload, 'schedules' => [$row, [...$row, 'room' => 'B']]])->assertUnprocessable()->assertJsonValidationErrors('schedules');
    $this->putJson($url, [...$payload, 'schedules' => ['invalid']])->assertUnprocessable();
});

it('allows enrollment corrections and reactivation while protecting grade ownership and seats', function () {
    $this->actingAs(User::factory()->registrar()->create());
    $enrollment = Enrollment::factory()->create(['status' => 'dropped']);
    $url = '/api/v1/enrollments/'.$enrollment->id;
    $this->patchJson($url.'/status', ['status' => 'enrolled'])->assertOk();
    $target = CourseOffering::factory()->create();
    $payload = ['student_id' => $enrollment->student_id, 'course_offering_id' => $target->id, 'enrollment_date' => '2026-09-01', 'status' => 'enrolled'];
    $this->putJson($url, $payload)->assertOk()->assertJsonPath('data.course_offering_id', $target->id)->assertJsonPath('data.enrollment_date', '2026-09-01');
    Grade::factory()->create(['enrollment_id' => $enrollment->id]);
    $this->putJson($url, [...$payload, 'course_offering_id' => $enrollment->course_offering_id])->assertConflict();
    $this->patchJson($url.'/status', ['status' => 'withdrawn'])->assertOk();
    $target->update(['capacity' => 1]);
    Enrollment::factory()->create(['course_offering_id' => $target->id]);
    $this->patchJson($url.'/status', ['status' => 'enrolled'])->assertConflict();
});

it('includes upcoming terms and the current student account in edit lookups', function () {
    $this->actingAs(User::factory()->registrar()->create());
    $user = User::factory()->student()->create();
    $student = Student::factory()->create(['user_id' => $user->id]);
    $term = AcademicTerm::factory()->create(['status' => 'upcoming']);
    $deletedCourse = Course::factory()->create();
    $deletedCourse->delete();
    $data = $this->getJson('/api/v1/lookups?student_id='.$student->id)->assertOk()->json('data');
    expect(array_column($data['academic_terms'], 'id'))->toContain($term->id);
    expect(array_column($data['student_users'], 'id'))->toContain($user->id);
    expect(array_column($data['courses'], 'id'))->not->toContain($deletedCourse->id);
    $student->delete();
    $data = $this->getJson('/api/v1/lookups')->assertOk()->json('data');
    expect(array_column($data['student_users'], 'id'))->not->toContain($user->id);
});

it('updates user roles and descriptions without breaking linked accounts', function () {
    $this->actingAs(User::factory()->administrator()->create());
    $user = User::factory()->create();
    $role = User::factory()->registrar()->create()->role_id;
    $payload = ['name' => $user->name, 'email' => $user->email, 'status' => 'active', 'role_id' => $role];
    $this->putJson('/api/v1/users/'.$user->id, $payload)->assertOk()->assertJsonPath('data.role.slug', 'registrar');
    $linked = User::factory()->student()->create();
    Student::factory()->create(['user_id' => $linked->id]);
    $this->putJson('/api/v1/users/'.$linked->id, [...$payload, 'email' => $linked->email])->assertUnprocessable()->assertJsonValidationErrors('role_id');
    $program = Program::factory()->create();
    $this->putJson('/api/v1/programs/'.$program->id, ['code' => $program->code, 'name' => $program->name, 'status' => 'active', 'description' => 'Updated description'])->assertOk()->assertJsonPath('data.description', 'Updated description');
});
