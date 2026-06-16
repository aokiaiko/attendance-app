<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in' => ['nullable'],
            'clock_out' => ['nullable'],
            'break_start.*' => ['nullable'],
            'break_end.*' => ['nullable'],
            'note'=>['required'],
        ];
    }

    public function messages()
    {
        return [
            'note.required' => '備考を記入してください',
        ];
    }

     public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');

            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add(
                    'work_time',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }
            
            $breakStarts = $this->input('break_start', []);
            $breakEnds = $this->input('break_end', []);

            foreach ($breakStarts as $index => $breakStart) {
                $breakEnd = $breakEnds[$index] ?? null;

                if ($breakStart && $clockIn && $breakStart < $clockIn) {
                    $validator->errors()->add(
                        'break_time',
                        '休憩時間が不適切な値です'
                    );
                }

                if ($breakStart &&  $clockOut && $breakStart > $clockOut) {
                    $validator->errors()->add(
                        'break_time',
                        '休憩時間が不適切な値です'
                    );
                }

                if ($breakEnd && $clockOut && $breakEnd > $clockOut) {
                    $validator->errors()->add(
                        'break_time',
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }
                
            }
        });
    }
    

}


