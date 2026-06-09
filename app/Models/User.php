<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The email address of the protected system administrator.
     * This account can never be deleted.
     */
    public const SYSTEM_ADMIN_EMAIL = 'admin@edu.com';

    /**
     * Boot the model — register a global deletion guard for the system admin.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            if ($user->email === self::SYSTEM_ADMIN_EMAIL) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                    'The system administrator account cannot be deleted.'
                );
            }
        });
    }

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'bio',
        'phone', 'telegram', 'birthday', 'birthplace', 'profession', 'biography'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Courses the user is enrolled in (many-to-many through enrollments).
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withTimestamps()
            ->withPivot('enrolled_at');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function lessonCompletions()
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function codingSubmissions()
    {
        return $this->hasMany(CodingSubmission::class);
    }

    public function teacherApplication()
    {
        return $this->hasOne(TeacherApplication::class);
    }

    public function withdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class);
    }

    public function teacherPayouts()
    {
        return $this->hasMany(TeacherPayout::class);
    }

    public function studentLists()
    {
        return $this->hasMany(StudentList::class);
    }

    public function isAdmin()

    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'instructor';
    }

    public function isInstructor()
    {
        return $this->role === 'instructor';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = ($value === 'teacher') ? 'instructor' : $value;
    }

    public function getTotalStudentsAttribute()
    {
        $courseIds = $this->courses()->pluck('id');
        if ($courseIds->isEmpty()) {
            return 0;
        }
        return \App\Models\Enrollment::whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function getBiographyAttribute()
    {
        return $this->bio;
    }

    public function setBiographyAttribute($value)
    {
        $this->attributes['bio'] = $value;
    }

    /**
     * Send the password reset notification using our branded Brevo email template.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, $this->email));
    }
}
