<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Http\Resources\CourseResource;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Auth;

class CourseController extends BaseController
{

    public function index()
    {

        $courses = Course::all(); // جلب جميع الدورات من قاعدة البيانات
        $cc=[];
        foreach($courses as $c){
            $instructor_id=$c->instructor_id;
            $instructor=Instructor::find($instructor_id);
            $c["instructor"]=$instructor;
            $cc[]=$c;
        }
        return $this->sendResponse($cc,'All courses');
        // return  $cc;

        //  $courses=Course::latest()->get() ;
    }



    public function trachedCourses()
    {
        $courses=Course::onlyTrashed()->latest()->get();
        return $this->sendResponse(CourseResource::collection($courses),'All courses');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function inrolledCourse(Request $request)
    {
        $courses=Course::all();
        $student=Student::findOrFail($request->id);
        $courses = $student->courses;

        $cc=[];
        foreach($courses as $c){
            $instructor_id=$c->instructor_id;
            $instructor=Instructor::find($instructor_id);
            $cc[]=[$c,$instructor->name];
        }
        return  $cc;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $input=$request->all();
        $user_id=Auth::user()->id;
        $user_type=Auth::user()->type;
        if(!$user_type=="instructor"){
            return $this->sendError('you haven`t permission to create new course!!!!');
        }else{
        $instructor=Instructor::where("user_id",$user_id)->firstOrFail();
        $instructor_id=$instructor->id;
        $input['instructor_id']=$instructor_id;
        $validate=validator::make($input,[
            'title'         => 'required',
            'description'   => 'required',
            'instructor_id' => 'required',
        ]);

        if($validate->fails()){
            return $this->sendError('Validate error', $validate->errors());
        }

        if ($image=$request->file('course_image')) {
            $destinationPath='images/courses/';
            $courseImage=date('YmdHis').".".$image->getClientOriginalExtension();
            $image->move($destinationPath,$courseImage);
            $input['course_image']=$courseImage;
        }

        $course=Course::create($input);
        return $this->sendResponse(new CourseResource($course),'Course added successfully');
    }}



    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course=Course::find($course->id);
        if(is_null($course)){
            return $this->sendError('course not found');
        }

        return $this->sendResponse(new CourseResource($course),'Course found successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $input=$request->all();
        $validate=validator::make($input,[
            'title' => 'required',
            'description' => 'required',
            'instructor_id' => 'required',
            'status' => 'required'
        ]);

        if($validate->fails()){
            return $this->sendError('Validate error', $validate->errors());
        }

        if ($image=$request->file('image')) {
            $destinationPath='images/courses';
            $courseImage=date('YmdHis').".".$image->getClientOriginalExtention();
            $image->move($destinationPath,$courseImage);
            $input['image']="$courseImage";
            $course->course_image=$input['course_image'];
        }else{
            unset($input['image']);
        }

        $course->status=$input['status'];
        $course->title=$input['title'];
        $course->description=$input['description'];
        $course->instructor_id=$input['instructor_id'];
        $course->start_at=$input['start_at'];
        $course->end_at=$input['end_at'];


        return $this->sendResponse(new CourseResource($course),'Course updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return $this->sendResponse(new CourseResource($course),'Course deleted successfully');
    }




    public function softDelete($id)
    {
        $course=Course::find($id)->delete();
        return $this->sendResponse(new CourseResource($course),'Course deleted successfully');

    }
    public function forceDelete($id)
    {
        $course=Course::onlyTrashed()->where('id',$id)->forceDelete();
        return $this->sendResponse(new CourseResource($course),'Course deleted successfully');

    }
    public function back($id)
    {
        $course=Course::onlyTrashed()->where('id',$id)->first()->restore();
        return $this->sendResponse(new CourseResource($course),'Course retreive successfully');
    }
}
