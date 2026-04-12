<script setup>
import { Link } from '@inertiajs/vue3';

const partnerSites = [
  {
    name: 'DopaStation',
    url: 'https://dopastation.com/',
    description: '도파민 가득한 커뮤니티',
    icon: '⚡'
  }
];

const currentYear = new Date().getFullYear();
</script>

<template>
  <footer class="bg-white border-t border-gray-100 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-12">
        
        <!-- Column 1: Brand -->
        <div class="md:col-span-12 lg:col-span-6">
          <Link :href="route('home')" class="inline-block mb-4">
            <span class="text-primary font-extrabold text-2xl tracking-tighter">ttip</span>
          </Link>
          <p class="text-gray-500 text-sm font-medium leading-relaxed max-w-sm mb-6">
            당신의 일상에 특별한 팁을 더하는 공간, ttip입니다.<br>
            유용한 정보와 커뮤니티의 즐거움을 함께 나누어보세요.
          </p>
          <p class="text-gray-400 text-xs font-semibold uppercase tracking-widest">
            &copy; {{ currentYear }} ttip. All rights reserved.
          </p>
        </div>

        <!-- Column 2: Legal -->
        <div class="md:col-span-6 lg:col-span-3">
          <h4 class="text-gray-900 font-bold text-sm mb-5 uppercase tracking-wider">법적 고지</h4>
          <ul class="space-y-3">
            <li>
              <Link :href="route('terms')" class="text-gray-500 hover:text-primary text-sm font-semibold transition-colors">이용약관</Link>
            </li>
            <li>
              <Link :href="route('privacy')" class="text-gray-500 hover:text-primary text-sm font-semibold transition-colors">개인정보처리방침</Link>
            </li>
          </ul>
        </div>

        <!-- Column 3: Support & Social -->
        <div class="md:col-span-6 lg:col-span-3">
          <h4 class="text-gray-900 font-bold text-sm mb-5 uppercase tracking-wider">커뮤니티 & 지원</h4>
          <ul class="space-y-3">
            <li>
              <a href="mailto:support@ttip.com" class="text-gray-500 hover:text-primary text-sm font-semibold transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                문의하기
              </a>
            </li>
            <!-- <li>
              <a href="#" class="text-gray-500 hover:text-primary text-sm font-semibold transition-colors flex items-center">
                <span class="mr-2">💬</span> 오픈 카카오톡
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-500 hover:text-primary text-sm font-semibold transition-colors flex items-center">
                <span class="mr-2">🛰️</span> 디스코드 서버
              </a>
            </li> -->
          </ul>
        </div>

      </div>
    </div>
  </footer>
</template>
