<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineOptions({ layout: MainLayout });
</script>

<template>
  <Head title="이용약관" />

  <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Header -->
      <div class="bg-gray-50/50 px-8 py-10 border-b border-gray-100">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">서비스 이용약관</h1>
        <p class="mt-2 text-gray-500 font-medium italic">시행일: 2026년 4월 12일</p>
      </div>

      <!-- Content -->
      <div class="px-8 py-12 prose prose-indigo max-w-none">
        <section class="mb-12">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-primary pl-4 mb-6">제 1 조 (목적)</h2>
          <p class="text-gray-600 leading-relaxed">
            이 약관은 ttip(이하 "회사"라 합니다)가 운영하는 인터넷 서비스(이하 "서비스"라 합니다)를 이용함에 있어 "회사"와 이용자의 권리, 의무 및 책임사항을 규정함을 목적으로 합니다.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-primary pl-4 mb-6">제 2 조 (용어의 정의)</h2>
          <ul class="list-disc pl-6 space-y-3 text-gray-600">
            <li><strong>서비스:</strong> 구현되는 단말기(PC, 휴대형단말기 등의 각종 유무선 장치를 포함)와 상관없이 "회원"이 이용할 수 있는 ttip 관련 제반 서비스를 의미합니다.</li>
            <li><strong>회원:</strong> "회사"의 "서비스"에 접속하여 이 약관에 따라 "회사"와 이용계약을 체결하고 "회사"가 제공하는 "서비스"를 이용하는 고객을 말합니다.</li>
            <li><strong>게시물:</strong> "회원"이 "서비스"를 이용함에 있어 "서비스"상에 게시한 부호, 문자, 음성, 음향, 화상, 동영상 등의 정보 형태의 글, 사진, 동영상 및 각종 파일과 링크 등을 의미합니다.</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-primary pl-4 mb-6">제 3 조 (이용계약 체결)</h2>
          <p class="text-gray-600 leading-relaxed">
            이용계약은 "회원"이 되고자 하는 자가 약관의 내용에 대하여 동의를 한 다음 회원가입신청을 하고 "회사"가 이러한 신청에 대하여 승낙함으로써 체결됩니다. "회사"는 가입신청자의 신청에 대하여 "서비스" 이용을 승낙함을 원칙으로 합니다. 다만, "회사"는 다음 각 호에 해당하는 신청에 대하여는 승낙을 하지 않거나 사후에 이용계약을 해지할 수 있습니다.
          </p>
          <ul class="list-decimal pl-6 mt-4 space-y-2 text-gray-600">
            <li>가입신청자가 이 약관에 의하여 이전에 회원자격을 상실한 적이 있는 경우</li>
            <li>실명이 아니거나 타인의 명의를 이용한 경우</li>
            <li>허위의 정보를 기재하거나, "회사"가 제시하는 내용을 기재하지 않은 경우</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-primary pl-4 mb-6">제 4 조 (회원의 서비스 이용 수칙)</h2>
          <p class="text-gray-600 leading-relaxed mb-4">"회원"은 "서비스" 이용 시 다음 각 호의 행위를 하여서는 안 됩니다.</p>
          <ul class="list-disc pl-6 space-y-2 text-gray-600">
            <li>타인의 정보도용 또는 허위사실 기재</li>
            <li>"회사" 및 기타 제3자의 저작권 등 지적재산권에 대한 침해</li>
            <li>공공질서 및 미풍양속에 위반되는 게시물의 게시</li>
            <li>"회사"의 동의 없이 영리를 목적으로 "서비스"를 사용하는 행위</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-primary pl-4 mb-6">제 5 조 (게시물의 저작권)</h2>
          <p class="text-gray-600 leading-relaxed">
            "회원"이 "서비스" 내에 게시한 "게시물"의 저작권은 해당 게시물의 저작자에게 귀속됩니다. 단, "회사"는 "서비스"의 운영, 전시, 홍보 및 개선을 위해 "회원"의 게시물을 "서비스" 내에서 이용할 수 있는 권한을 가집니다.
          </p>
        </section>

        <section class="mb-4">
          <h2 class="text-xl font-bold text-gray-900 border-l-4 border-primary pl-4 mb-6">제 6 조 (책임의 한계)</h2>
          <p class="text-gray-600 leading-relaxed">
            "회사"는 천재지변 또는 이에 준하는 불가항력으로 인하여 "서비스"를 제공할 수 없는 경우에는 "서비스" 제공에 관한 책임이 면제됩니다. 또한 "회사"는 "회원"이 게시한 정보의 신뢰도, 정확성 등의 내용에 관하여는 책임을 지지 않습니다.
          </p>
        </section>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50/50 px-8 py-6 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-500 font-medium">본 약관에 대해 궁금하신 점이 있다면 <a href="mailto:support@ttip.com" class="text-primary hover:underline font-bold">고객지원센터</a>로 문의해 주세요.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.prose h2 {
    margin-top: 0;
    margin-bottom: 1.5rem;
}
</style>
