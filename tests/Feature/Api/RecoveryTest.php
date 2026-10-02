<?php

use App\Enums\RecoverableType;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;

it('soft deletes and recovers the same record without purging it', function (RecoverableType $type) {
    $this->actingAs(User::factory()->administrator()->create());
    $class = $type->modelClass();
    $record = $class::factory()->create();
    $this->deleteJson('/api/v1/'.$type->value.'/'.$record->id)->assertNoContent();
    $this->assertSoftDeleted($record);
    expect($class::query()->find($record->id))->toBeNull();
    $this->getJson('/api/v1/recycle-bin?type='.$type->value)->assertOk()->assertJsonPath('data.0.id', $record->id);
    $this->postJson('/api/v1/recycle-bin/'.$type->value.'/'.$record->id.'/restore')->assertOk();
    expect($class::query()->find($record->id))->not->toBeNull();
    $this->assertDatabaseCount($record->getTable(), $class::withTrashed()->count());
    $this->getJson('/api/v1/recycle-bin?type='.$type->value)->assertOk()->assertJsonCount(0, 'data');
    $this->postJson('/api/v1/recycle-bin/'.$type->value.'/'.$record->id.'/restore')->assertNotFound();
})->with(RecoverableType::cases());

it('restricts recovery and deletion by role', function () {
    $record = Course::factory()->create();
    $this->deleteJson('/api/v1/courses/'.$record->id)->assertUnauthorized();
    $this->getJson('/api/v1/recycle-bin?type=courses')->assertUnauthorized();
    $this->actingAs(User::factory()->student()->create())->deleteJson('/api/v1/courses/'.$record->id)->assertForbidden();
    $this->actingAs(User::factory()->instructor()->create())->getJson('/api/v1/recycle-bin?type=courses')->assertForbidden();
    $admin = User::factory()->administrator()->create();
    $this->actingAs(User::factory()->registrar()->create())->deleteJson('/api/v1/users/'.$admin->id)->assertForbidden();
    $this->getJson('/api/v1/recycle-bin?type=users')->assertForbidden();
    $this->deleteJson('/api/v1/courses/'.$record->id)->assertNoContent();
    $this->actingAs(User::factory()->student()->create())->postJson('/api/v1/recycle-bin/courses/'.$record->id.'/restore')->assertForbidden();
    $this->actingAs(User::factory()->registrar()->create())->postJson('/api/v1/recycle-bin/courses/'.$record->id.'/restore')->assertOk();
});

it('never deletes grades or an enrollment containing grades', function () {
    $this->actingAs(User::factory()->administrator()->create());
    $grade = Grade::factory()->create();
    $enrollment = $grade->enrollment;
    $this->deleteJson('/api/v1/grades/'.$grade->id)->assertNotFound();
    $this->deleteJson('/api/v1/course-offerings/'.$enrollment->course_offering_id.'/grades')->assertStatus(405);
    $this->deleteJson('/api/v1/enrollments/'.$enrollment->id)->assertConflict();
    $this->deleteJson('/api/v1/students/'.$enrollment->student_id)->assertConflict();
    $this->deleteJson('/api/v1/course-offerings/'.$enrollment->course_offering_id)->assertConflict();
    $this->getJson('/api/v1/recycle-bin?type=grades')->assertUnprocessable();
    $this->assertDatabaseHas('grades', ['id' => $grade->id, 'enrollment_id' => $enrollment->id]);
    expect($enrollment->fresh()->trashed())->toBeFalse();
});

it('retains a grade record even when its editable fields are cleared', function () {
    $this->actingAs(User::factory()->registrar()->create());
    $grade = Grade::factory()->create();
    $enrollment = $grade->enrollment;
    $this->putJson('/api/v1/course-offerings/'.$enrollment->course_offering_id.'/grades', ['grades' => [[
        'enrollment_id' => $enrollment->id, 'midterm_grade' => null, 'final_grade' => null, 'remarks' => null,
    ]]])->assertOk();
    $this->assertDatabaseHas('grades', ['id' => $grade->id, 'enrollment_id' => $enrollment->id, 'midterm_grade' => null, 'final_grade' => null]);
    $this->assertDatabaseCount('grades', 1);
    $this->deleteJson('/api/v1/enrollments/'.$enrollment->id)->assertConflict();
});

it('keeps archived users out of authentication and protects the current account', function () {
    $admin = User::factory()->administrator()->create();
    $user = User::factory()->create();
    $this->actingAs($admin)->deleteJson('/api/v1/users/'.$admin->id)->assertConflict();
    $this->deleteJson('/api/v1/users/'.$user->id)->assertNoContent();
    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password'])->assertUnprocessable();
});

it('requires deleted parents to be recovered before their children', function () {
    $this->actingAs(User::factory()->registrar()->create());
    $student = Student::factory()->create();
    $this->deleteJson('/api/v1/programs/'.$student->program_id)->assertConflict();
    $this->deleteJson('/api/v1/students/'.$student->id)->assertNoContent();
    $this->deleteJson('/api/v1/programs/'.$student->program_id)->assertNoContent();
    $this->postJson('/api/v1/recycle-bin/students/'.$student->id.'/restore')->assertConflict();
    $this->postJson('/api/v1/recycle-bin/programs/'.$student->program_id.'/restore')->assertOk();
    $this->postJson('/api/v1/recycle-bin/students/'.$student->id.'/restore')->assertOk();
});

it('rechecks enrollment capacity on recovery and reserves archived identities', function () {
    $this->actingAs(User::factory()->registrar()->create());
    $offering = CourseOffering::factory()->create(['capacity' => 1]);
    $enrollment = Enrollment::factory()->create(['course_offering_id' => $offering->id]);
    $this->deleteJson('/api/v1/enrollments/'.$enrollment->id)->assertNoContent();
    $this->postJson('/api/v1/enrollments', ['student_id' => $enrollment->student_id, 'course_offering_ids' => [$offering->id]])->assertConflict();
    $other = Enrollment::factory()->create(['course_offering_id' => $offering->id]);
    $this->postJson('/api/v1/recycle-bin/enrollments/'.$enrollment->id.'/restore')->assertConflict();
    $this->patchJson('/api/v1/enrollments/'.$other->id.'/status', ['status' => 'dropped'])->assertOk();
    $this->postJson('/api/v1/recycle-bin/enrollments/'.$enrollment->id.'/restore')->assertOk();
});

it('validates recovery queries and paginates matching deleted records only', function () {
    $this->actingAs(User::factory()->registrar()->create());
    Course::factory()->count(3)->create(['course_title' => 'Archived science'])->each->delete();
    Course::factory()->create(['course_title' => 'Archived science']);
    $this->getJson('/api/v1/recycle-bin?type=courses&search=Archived&per_page=2')->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('meta.total', 3);
    $this->getJson('/api/v1/recycle-bin?type=courses&page=2&per_page=2')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/recycle-bin?type=unknown')->assertUnprocessable();
    $this->getJson('/api/v1/recycle-bin?type=courses&per_page=0')->assertUnprocessable();
    $this->deleteJson('/api/v1/courses/999999')->assertNotFound();
    $this->postJson('/api/v1/recycle-bin/courses/999999/restore')->assertNotFound();
});

it('rejects archived foreign keys and preserves unique codes for recovery', function () {
    $this->actingAs(User::factory()->registrar()->create());
    $program = Program::factory()->create();
    $program->delete();
    $this->postJson('/api/v1/programs', ['code' => $program->code, 'name' => 'Duplicate', 'status' => 'active'])->assertUnprocessable()->assertJsonValidationErrors('code');
    $this->postJson('/api/v1/students', ['program_id' => $program->id, 'student_number' => 'NEW-001', 'first_name' => 'New', 'last_name' => 'Student', 'year_level' => 1, 'status' => 'active'])->assertUnprocessable()->assertJsonValidationErrors('program_id');
});
