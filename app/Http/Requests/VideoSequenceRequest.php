<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoSequenceRequest extends FormRequest
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
        return [];
    }

    public function getParsedData()
    {
        $data = $this->only(['duration', 'timestamp', 'video_nid', 'title', 'description', 'camera_look_at', 'camera_locked', 'camera_path', 'camera_yaw', 'camera_pitch']);
        $additional_fields = $this->except([
            'duration',
            'timestamp',
            'video_nid',
            'title',
            'description',
            'user_id',
            'id',
            'camera_look_at',
            'camera_locked',
            'camera_path',
            'camera_yaw',
            'camera_pitch'
        ]);
        $data['additional_fields'] = $additional_fields;
        return $data;
    }
}
