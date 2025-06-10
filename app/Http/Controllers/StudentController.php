<?php

namespace App\Http\Controllers;

use App\Models\student;
use App\Models\Course;
use App\Models\User;
use App\Http\Resources\StudentResource;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\student_courses;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Auth;

class StudentController extends BaseController
{
    public function index()
    {
        $students = Student::latest()->get();
        foreach ($students as $s) {
            $students_images = $s->student_image;
            $s["student_image"] = $students_images;
        }
        return $this->sendResponse(BaseController::collection($students), 'All students');
    }

    public function trachedStudents()
    {
        $students = Student::onlyTrashed()->latest()->get();
        return $this->sendResponse(StudentController::collection($students), 'All trached students');
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_type = Auth::user()->type;
        if ($user_type == 'student') {           // If the user who wants to create a student account is a Student
            $input = $request->all();
            $user_id = Auth::user()->id;
            $input["user_id"] = $user_id;
            $validate = validator::make($input, [
                'name' => 'required',
                'student_image' => 'required',
                'address' => 'required',
                'bio' => 'required',
                'phone' => 'required',
                'user_id' => 'required'
            ]);

            if ($validate->fails()) {
                return $this->sendError('Validate error', $validate->errors());
            }

            if ($image = $request->file('student_image')) {
                // echo "yes";
                $destinationPath = 'images/students/';
                $studentImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $studentImage);
                $input['student_image'] = $studentImage;
            }

            $student = Student::create($input);
            return $this->sendResponse($student, 'student added successfully');
        } elseif ($user_type == 'admin') {         // If the user who wants to create a student account is a Admin

            DB::beginTransaction();

            try {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required',
                    'c_password' => 'required|same:password',
                ]);

                if ($validator->fails()) {
                    return $this->sendError('Validation error', $validator->errors());
                }

                $input = $request->only(['name', 'email', 'password']);
                $input['password'] = Hash::make($input['password']);
                $input['type'] = 0;
                $user = User::create($input);
                $success['token'] = $user->createToken('tokenKey')->accessToken;
                $success['name'] = $user->name;

                // this execute if the above success
                $input = $request->all();
                $input['user_id'] = $user->id; // Use the newly created user's ID
                $validate = Validator::make($input, [
                    'name' => 'required',
                    'student_image' => 'required',
                    'address' => 'required',
                    'bio' => 'required',
                    'phone' => 'required',
                ]);

                if ($validate->fails()) {
                    DB::rollBack();
                    return $this->sendError('Validation error', $validate->errors());
                }

                if ($image = $request->file('student_image')) {
                    $destinationPath = 'images/students/';
                    $studentImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                    $image->move($destinationPath, $studentImage);
                    $input['student_image'] = $studentImage;
                }

                $student = Student::create($input);
                DB::commit();

                return $this->sendResponse($student, 'Student added successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->sendError('An error occurred', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $courses = student_courses::where("student_id", $id)->get("id");
        foreach ($courses as $idc) {
            $course = Course::find($idc);
            $res["$idc"] = $course;
        }
        $student = Student::find($id);
        $u_id = $student['user_id'];      // get the user_id which belongs this student
        $auth_id = auth()->user()->id;    // get user id who want to show this page (id from users tabel)
        $type = auth()->user()->type;     // get user type want to show this page (id from users tabel)
        if (is_null($student)) {
            return $this->sendError('student not found');
        }
        if ($auth_id == $u_id || $type == "admin") {          // The student can only see his profile and admin can see all student profiles
            return $this->sendResponse(new StudentResource($student), 'student found successfully');
        } else {
            return $this->sendError('You do not have permission to access for this page');
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::all();
        $student = Student::find($id);            // find the student who has this id
        $u_id = $student['user_id'];              // get the user_id which belongs this student
        $auth_id = auth()->user()->id;            // get user id who want to show this page (id from users tabel)
        $type = auth()->user()->type;             // get user type want to show this page (id from users tabel)
        if ($auth_id == $u_id || $type == "admin") {
            $input = $request->all();
            $validate = validator::make($input, [
                'name' => 'required',
                'student_image' => 'required',
                'address' => 'required',
                'bio' => 'required',
                'phone' => 'required'
            ]);

            if ($validate->fails()) {
                return $this->sendError('Validate error', $validate->errors());
            }

            if ($image = $request->file('student_image')) {
                $destinationPath = 'images/students/';
                $studentImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $studentImage);
                $input['student_image'] = "$studentImage";
                $student->student_image = $input['student_image'];
            } else {
                unset($input['image']);
            }
            $student = Student::findOrFail($request->id);
            $user = $student->user;
            $password = $user->password;
            if ($password == $request->password) {
                $user->password = $input['password'];
            } else {
                unset($input['password']);
            }

            $student->name = $input['name'];
            $student->address = $input['address'];
            $student->bio = $input['bio'];
            $student->phone = $input['phone'];
            $student->fill($input);
            $student->save(); // Save the changes into the database
            $user->save();

            return $this->sendResponse([$student, $user->password], 'student updated successfully');
        } else {
            return $this->sendError('You do not have permission to access for this page');
        }
    }

    public function softDelete($id)
    {
        $student = Student::find($id);                // find the student who has this id
        $u_id = $student['user_id'];                  // get the user_id which belongs this student
        $auth_id = auth()->user()->id;                // get user id who want to show this page (id from users tabel)
        $type = auth()->user()->type;                 // get user type want to show this page (id from users tabel)
        if ($auth_id == $u_id || $type == "admin") {
            $student = Student::find($id)->delete();
            $user = User::find($u_id)->delete();


            return $this->sendResponse($student, 'student deleted successfully');
        } else {
            return $this->sendError('You do not have permission to delete this student');
        }
    }

    public function forceDelete($id)
    {
        $student = Student::onlyTrashed()->where('id', $id)->first();
        if (!$student) {
            return $this->sendError('لم يتم العثور على الطالب');
        }

        $u_id = $student->user_id;
        $student->forceDelete();
        User::onlyTrashed()->where('id', $u_id)->forceDelete();

        return $this->sendResponse($student, 'تم حذف الطالب بنجاح');
    }



    public function back($id)
    {
        $student = Student::onlyTrashed()->where('id', $id)->first()->restore();
        return $this->sendResponse($student, 'student retreive successfully');
    }
}
