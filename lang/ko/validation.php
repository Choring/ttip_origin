<?php

return [
    // ── 기본 규칙 ──────────────────────────────────────────────────────────
    'accepted'             => ':attribute 항목에 동의해야 합니다.',
    'accepted_if'          => ':other 값이 :value 일 때 :attribute 에 동의해야 합니다.',
    'array'                => ':attribute 항목은 배열이어야 합니다.',
    'boolean'              => ':attribute 필드는 참(true) 또는 거짓(false) 값이어야 합니다.',
    'confirmed'            => ':attribute 확인이 일치하지 않습니다.',
    'date'                 => ':attribute 필드는 올바른 날짜 형식이어야 합니다.',
    'different'            => ':attribute 필드와 :other 필드는 달라야 합니다.',
    'digits'               => ':attribute 필드는 :digits 자리 숫자여야 합니다.',
    'email'                => ':attribute 필드는 유효한 이메일 주소여야 합니다.',
    'exists'               => '선택하신 :attribute 값이 유효하지 않습니다.',
    'file'                 => ':attribute 필드는 파일이어야 합니다.',
    'image'                => ':attribute 파일은 이미지(jpg, jpeg, png, gif, webp) 형식이어야 합니다.',
    'in'                   => '선택하신 :attribute 값이 허용된 범위를 벗어났습니다.',
    'integer'              => ':attribute 필드는 정수여야 합니다.',
    'json'                 => ':attribute 필드는 올바른 JSON 문자열이어야 합니다.',
    'max'                  => [
        'array'   => ':attribute 항목은 최대 :max 개를 초과할 수 없습니다.',
        'file'    => ':attribute 파일 크기는 최대 :max 킬로바이트를 초과할 수 없습니다.',
        'numeric' => ':attribute 값은 최대 :max 를 초과할 수 없습니다.',
        'string'  => ':attribute 필드는 최대 :max 자를 초과할 수 없습니다.',
    ],
    'mimes'                => ':attribute 파일은 :values 형식이어야 합니다.',
    'mimetypes'            => ':attribute 파일은 :values 형식이어야 합니다.',
    'min'                  => [
        'array'   => ':attribute 항목은 최소 :min 개 이상이어야 합니다.',
        'file'    => ':attribute 파일 크기는 최소 :min 킬로바이트 이상이어야 합니다.',
        'numeric' => ':attribute 값은 최소 :min 이상이어야 합니다.',
        'string'  => ':attribute 필드는 최소 :min 자 이상이어야 합니다.',
    ],
    'not_in'               => '선택하신 :attribute 값이 올바르지 않습니다.',
    'nullable'             => ':attribute 필드는 비워둘 수 있습니다.',
    'numeric'              => ':attribute 필드는 숫자여야 합니다.',
    'required'             => ':attribute 필드는 필수 입력 항목입니다.',
    'required_if'          => ':other 값이 :value 일 때 :attribute 필드는 필수입니다.',
    'required_unless'      => ':other 값이 :values 가 아니면 :attribute 필드는 필수입니다.',
    'required_with'        => ':values 필드가 있을 때 :attribute 필드는 필수입니다.',
    'same'                 => ':attribute 필드와 :other 필드가 일치해야 합니다.',
    'size'                 => [
        'array'   => ':attribute 항목은 정확히 :size 개여야 합니다.',
        'file'    => ':attribute 파일 크기는 정확히 :size 킬로바이트여야 합니다.',
        'numeric' => ':attribute 값은 :size 여야 합니다.',
        'string'  => ':attribute 필드는 정확히 :size 자여야 합니다.',
    ],
    'string'               => ':attribute 필드는 문자열이어야 합니다.',
    'unique'               => '이미 사용 중인 :attribute 입니다.',
    'url'                  => ':attribute 필드는 올바른 URL 형식이어야 합니다.',

    // ── 비밀번호 ────────────────────────────────────────────────────────────
    'password' => [
        'letters'       => '비밀번호는 적어도 하나의 영문자를 포함해야 합니다.',
        'mixed'         => '비밀번호는 대문자와 소문자를 각각 하나 이상 포함해야 합니다.',
        'numbers'       => '비밀번호는 적어도 하나의 숫자를 포함해야 합니다.',
        'symbols'       => '비밀번호는 적어도 하나의 특수문자를 포함해야 합니다.',
        'uncompromised' => '입력하신 비밀번호는 이전에 유출된 적이 있습니다. 다른 비밀번호를 선택해 주세요.',
        'min'           => '비밀번호는 최소 :min 자리 이상이어야 합니다.',
    ],

    // ── 필드명 한국어 매핑 ──────────────────────────────────────────────────
    'attributes' => [
        // 인증 관련
        'email'                => '이메일 주소',
        'password'             => '비밀번호',
        'password_confirmation'=> '비밀번호 확인',
        'current_password'     => '현재 비밀번호',
        'name'                 => '닉네임',
        // 게시글
        'title'                => '제목',
        'content'              => '본문',
        'category_id'          => '카테고리',
        'tags'                 => '태그',
        'tags.*'               => '태그',
        'image'                => '이미지',
        'type'                 => '게시글 타입',
        'is_pinned'            => '상단 고정',
        'extra_info'           => '정형 정보',
        // 댓글
        'parent_id'            => '상위 댓글',
        // 공지
        'summary'              => '요약',
    ],
];
