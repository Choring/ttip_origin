<?php

return [
    'confirmed' => ':attribute 확인이 일치하지 않습니다.',
    'email' => ':attribute 형식은 유효한 이메일 주소여야 합니다.',
    'lowercase' => ':attribute 필드는 소문자여야 합니다.',
    'max' => [
        'string' => ':attribute 필드는 :max 자를 초과할 수 없습니다.',
    ],
    'min' => [
        'array' => ':attribute 항목은 최소 :min 개 이상이어야 합니다.',
        'file' => ':attribute 파일은 최소 :min 킬로바이트 이상이어야 합니다.',
        'numeric' => ':attribute 값은 최소 :min 이상이어야 합니다.',
        'string' => ':attribute 문자는 최소 :min 자 이상이어야 합니다.',
    ],
    'required' => ':attribute 필드는 필수 입력 항목입니다.',
    'string' => ':attribute 필드는 문자열이어야 합니다.',
    'unique' => '이미 사용 중인 :attribute 입니다.',

    'password' => [
        'letters' => '비밀번호는 적어도 하나의 영문자를 포함해야 합니다.',
        'mixed' => '비밀번호는 대문자와 소문자를 각각 하나 이상 포함해야 합니다.',
        'numbers' => '비밀번호는 적어도 하나의 숫자를 포함해야 합니다.',
        'symbols' => '비밀번호는 적어도 하나의 특수문자를 포함해야 합니다.',
        'uncompromised' => '입력하신 비밀번호는 이전에 유출된 적이 있습니다. 다른 비밀번호를 선택해 주세요.',
        'min' => '비밀번호는 최소 :min 자리 이상이어야 합니다.',
    ],

    'attributes' => [
        'email' => '이메일 주소',
        'password' => '비밀번호',
        'password_confirmation' => '비밀번호 확인',
        'name' => '닉네임',
        'current_password' => '현재 비밀번호',
    ],
];
