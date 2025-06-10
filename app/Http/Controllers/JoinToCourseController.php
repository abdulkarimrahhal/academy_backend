<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\student;
use App\Models\Course;
use App\Models\student_courses;
use Auth;
use App\Http\Resources\CourseResource;
use App\Http\Controllers\API\BaseController as BaseController;

class JoinToCourseController extends BaseController
{

    public function enrollStudentToCourse(Request $request)
    {
        $user_type = Auth::user()->type;
        if ($user_type == "student") {
            $user_id = Auth::user()->id;
            $student = Student::where("user_id", $user_id)->first();
            $course = Course::find($request->course_id);
            if ($d = $student->courses->contains($request->course_id)) {
                return $this->sendError('student is already enrolled in the course');
            } else {
                $student->courses()->attach($course);
                return response()->json([$d, $student->id, 'message' => 'Student enrolled to course successfully!', $course]);
            }
        } else {
            return $this->sendError('you haven`t permission to enroll in this course !');
        }
    }

    public function withdrawStudentFromCourse(Request $request)
    {
        $student = Student::find($request->student_id);
        $course = Course::find($request->course_id);

        // Withdraw the student from the course
        $student->courses()->detach($course);

        return response()->json(['message' => 'Student withdrawn from course successfully!']);
    }


    public function inrolledCourse(Request $request)
    {
        $user_id = Auth::user()->id;
        $student_id = $request->get('student_id');
        $courses = student_courses::where("student_id", $student_id)->get();
        return $this->sendResponse(new CourseResource($courses), 'Course found successfully');
    }
}
