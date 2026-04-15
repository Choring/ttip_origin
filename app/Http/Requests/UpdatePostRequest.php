<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:200',
            'content'     => 'required|string',
            'tags'        => 'required|array|min:1|max:3',
            'tags.*'      => 'required|string|max:15',
            'type'        => 'nullable|string|in:general,notice,ad,pinned',
            'is_pinned'   => 'nullable|boolean',
            'extra_info'  => 'nullable|array',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => '카테고리를 선택해 주세요.',
            'category_id.exists'   => '유효하지 않은 카테고리입니다.',
            'title.required'       => '제목을 입력해 주세요.',
            'title.max'            => '제목은 200자를 초과할 수 없습니다.',
            'content.required'     => '본문을 입력해 주세요.',
            'tags.required'        => '태그를 1개 이상 입력해 주세요.',
            'tags.min'             => '태그를 1개 이상 입력해 주세요.',
            'tags.max'             => '태그는 최대 3개까지 입력할 수 있습니다.',
            'tags.*.max'           => '태그 하나는 최대 15자까지 입력할 수 있습니다.',
            'image.image'          => '이미지 파일만 업로드할 수 있습니다. (jpg, png, gif, webp)',
            'image.mimes'          => '이미지는 jpg, png, gif, webp 형식만 업로드할 수 있습니다.',
            'image.max'            => '이미지 파일 크기는 5MB를 초과할 수 없습니다.',
        ];
    }
}
