<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string'],
            'location'      => ['required', 'string'],
            'date'          => ['required', 'date', 'after_or_equal:today'],
            'start_time'    => [
                'required',
                'date_format:H:i',

                function ($attribute, $value, $fail) {
                    $dateInput = $this->input('date');

                    if (!$dateInput || !$value) {
                        return;
                    }

                    $selectedDate = Carbon::parse($dateInput);

                    if ($selectedDate->isToday()) {
                        $minTime = Carbon::now()->addHours(3);
                        $minute  = $minTime->minute;
                        $second  = $minTime->second;

                        if ($second > 0 || ($minute != 0 && $minute != 30)) {
                            if ($minute < 30) {
                                $minTime->minute(30)->second(0);
                            } else {
                                $minTime->addHour()->minute(0)->second(0);
                            }
                        }

                        $selectedDateTime = \Carbon\Carbon::parse($selectedDate->toDateString() . ' ' . $value);
                        if ($selectedDateTime->lessThan($minTime)) {
                            $fail("If the date is today, the start time must be at least {$minTime->format('H:i')}.");
                        }
                    }
                },
            ],
            'end_time'      => ['required', 'date_format:H:i', 'after:start_time'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
            'booking_limit' => ['nullable', 'integer', 'min:1'],
            'cover_image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery'       => ['nullable', 'array'],
            'gallery.*'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
