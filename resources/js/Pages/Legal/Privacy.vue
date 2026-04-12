<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineOptions({ layout: MainLayout });
</script>

<template>
  <Head title="개인정보처리방침" />

  <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Header -->
      <div class="bg-indigo-50/50 px-8 py-10 border-b border-gray-100">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">개인정보처리방침</h1>
        <p class="mt-2 text-gray-500 font-medium italic">시행일: 2026년 4월 12일</p>
      </div>

      <!-- Content -->
      <div class="px-8 py-12 prose prose-indigo max-w-none">
        <section class="mb-12">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-6">제 1 조 (개인정보의 처리 목적)</h2>
          <p class="text-gray-600 leading-relaxed">
            ttip(이하 "회사"라 함)은 다음의 목적을 위하여 개인정보를 처리합니다. 처리하고 있는 개인정보는 다음의 목적 이외의 용도로는 이용되지 않으며 이용 목적이 변경되는 경우에는 별도의 동의를 받는 등 필요한 조치를 이행할 예정입니다.
          </p>
          <ul class="list-disc pl-6 mt-4 space-y-2 text-gray-600">
            <li>회원 가입 및 관리: 회원 가입 의사 확인, 회원제 서비스 제공에 따른 본인 식별·인증, 회원자격 유지·관리 등</li>
            <li>서비스 제공: 게시물 작성, 댓글 서비스, 추천 서비스 등 기능 제공</li>
            <li>마케팅 및 광고에의 활용: 신규 서비스 개발 및 맞춤 서비스 제공, 이벤트 및 광고성 정보 제공 등</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-6">제 2 조 (개인정보의 처리 및 보유 기간)</h2>
          <p class="text-gray-600 leading-relaxed">
            "회사"는 법령에 따른 개인정보 보유·이용기간 또는 정보주체로부터 개인정보를 수집 시에 동의 받은 개인정보 보유·이용기간 내에서 개인정보를 처리·보유합니다.
          </p>
          <ul class="list-disc pl-6 mt-4 space-y-2 text-gray-600">
            <li>홈페이지 회원 가입 및 관리: 홈페이지 탈퇴 시까지</li>
            <li>관련 법령에 의한 보존: 통신확인자료(로그인 기록) 3개월, 소비자의 불만 또는 분쟁처리에 관한 기록 3년 등</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-6">제 3 조 (처리하는 개인정보의 항목)</h2>
          <p class="text-gray-600 leading-relaxed mb-4">"회사"는 다음의 개인정보 항목을 처리하고 있습니다.</p>
          <ul class="list-decimal pl-6 space-y-2 text-gray-600 font-medium">
            <li>필수항목: 이메일 주소, 비밀번호, 닉네임, 성명</li>
            <li>인터넷 서비스 이용 과정에서 자동으로 생성되어 수집될 수 있는 항목: IP주소, 쿠키, 서비스 이용 기록, 방문 기록 등</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-6">제 4 조 (개인정보의 파기절차 및 파기방법)</h2>
          <p class="text-gray-600 leading-relaxed">
            "회사"는 개인정보 보유기간의 경과, 처리목적 달성 등 개인정보가 불필요하게 되었을 때에는 지체 없이 해당 개인정보를 파기합니다. 전자적 파일 형태의 정보는 기록을 재생할 수 없는 기술적 방법을 사용하며, 종이 문서에 기록된 개인정보는 분쇄기로 분쇄하거나 소각하여 파기합니다.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-6">제 5 조 (정보주체의 권리·의무 및 그 행사방법)</h2>
          <p class="text-gray-600 leading-relaxed">
            정보주체는 언제든지 개인정보 열람·정정·삭제·처리정지 요구 등의 권리를 행사할 수 있습니다. 권리 행사는 서면, 전자우편 등을 통하여 하실 수 있으며 "회사"는 이에 대해 지체 없이 조치하겠습니다.
          </p>
        </section>

        <section class="mb-4">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-indigo-500 pl-4 mb-6">제 6 조 (개인정보 보호책임자)</h2>
          <p class="text-gray-600 leading-relaxed">
            개인정보 처리에 관한 업무를 총괄해서 책임지고, 관련 불만처리 및 피해구제 등을 위하여 아래와 같이 개인정보 보호책임자를 지정하고 있습니다.
          </p>
          <div class="mt-4 p-5 bg-gray-50 rounded-2xl border border-gray-100">
            <ul class="space-y-1 text-sm text-gray-600">
              <li><span class="font-bold text-gray-900">성명:</span> 관리자</li>
              <li><span class="font-bold text-gray-900">연락처:</span> support@ttip.com</li>
            </ul>
          </div>
        </section>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50/50 px-8 py-6 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-500 font-medium">ttip은 사용자의 개인정보를 소중히 여기며, 안전하게 보호하기 위해 최선을 다합니다.</p>
      </div>
    </div>
  </div>
</template>
