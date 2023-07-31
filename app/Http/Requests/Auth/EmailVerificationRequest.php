<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class EmailVerificationRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    if ($this->user()->verification_code !== $this->input('verification_code')) {
      return false;
    }

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
      //
    ];
  }

  /**
   * Fulfill the email verification request.
   *
   * @return void
   */
  public function fulfill()
  {
    if (!$this->user()->hasVerifiedEmail()) {
      $this->user()->markEmailAsVerified();

      event(new Verified($this->user()));
    }
  }

  /**
   * Configure the validator instance.
   *
   * @param  \Illuminate\Validation\Validator  $validator
   * @return void
   */
  public function withValidator(Validator $validator)
  {
    return $validator;
  }
}
