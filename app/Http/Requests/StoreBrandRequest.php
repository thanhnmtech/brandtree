<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['required', 'string', 'max:255'],
            'target_market' => ['required', 'string', 'max:255'],
            'founded_year' => ['required', 'integer', 'min:1901', 'max:' . date('Y')],
            'description' => ['required', 'string', 'max:5000'],
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên thương hiệu.',
            'name.max' => 'Tên thương hiệu không được quá 255 ký tự.',
            'industry.required' => 'Vui lòng nhập ngành nghề.',
            'target_market.required' => 'Vui lòng nhập thị trường mục tiêu.',
            'founded_year.required' => 'Vui lòng nhập năm thành lập.',
            'founded_year.min' => 'Năm thành lập không hợp lệ.',
            'founded_year.max' => 'Năm thành lập không được lớn hơn năm hiện tại.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'description.max' => 'Mô tả không được quá 5000 ký tự.',
            'logo.required' => 'Vui lòng tải lên logo thương hiệu.',
            'logo.image' => 'Logo phải là hình ảnh.',
            'logo.mimes' => 'Logo phải có định dạng: jpeg, png, jpg, gif, svg.',
            'logo.max' => 'Logo không được lớn hơn 2MB.',
        ];
    }
}
