<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaybackCommandRequest extends FormRequest
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
        $data = $this->only(['duration', 'timestamp', 'video_nid', 'title', 'type','sequence_id']);
        $additional_fields = $this->except([
            'duration',
            'timestamp',
            'video_nid',
            'title',
            'type',
            'date_formatted',
            'userdata',
            'id',
            'perms',
        ]);
        $data['additional_fields'] = $additional_fields;
        return $data;
    }
}
