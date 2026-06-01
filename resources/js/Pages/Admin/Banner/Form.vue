<template>
  <Head :title="banner ? '배너 수정 - Admin' : '배너 추가 - Admin'" />
  <AdminLayout>
    <div class="p-6 max-w-2xl">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-black text-gray-900">{{ banner ? '배너 수정' : '배너 추가' }}</h1>
        <Link :href="route('admin.banners.index')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
          ← 목록으로 돌아가기
        </Link>
      </div>

      <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-5 bg-white rounded-xl border border-gray-200 p-6">

        <!-- 제목 -->
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">메인 문구 <span class="text-red-500">*</span></label>
          <input v-model="form.title" type="text" placeholder="대구의 다양한 공연과 행사를 만나보세요"
            class="w-full border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-indigo-400"
          />
          <p v-if="errors.title" class="text-red-500 text-xs mt-1">{{ errors.title }}</p>
        </div>

        <!-- 부제목 -->
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">부제목 / 장소</label>
          <input v-model="form.subtitle" type="text" placeholder="대구광역시"
            class="w-full border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-indigo-400"
          />
        </div>

        <!-- 배경 이미지 업로드 -->
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">
            배경 이미지 <span class="text-red-500">{{ banner ? '' : '*' }}</span>
          </label>

          <!-- 업로드 영역 -->
          <div
            @click="$refs.fileInput.click()"
            @dragover.prevent
            @drop.prevent="onDrop"
            class="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-colors"
            :class="previewUrl ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50'"
          >
            <!-- 미리보기 -->
            <div v-if="previewUrl" class="relative">
              <img :src="previewUrl" alt="미리보기"
                class="w-full h-40 object-cover rounded-lg"
              />
              <button type="button" @click.stop="removeImage"
                class="absolute top-2 right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center text-xs font-bold hover:bg-red-600 transition-colors"
              >✕</button>
              <p class="text-xs text-indigo-500 mt-2 font-semibold">클릭하여 이미지 변경</p>
            </div>

            <!-- 업로드 안내 -->
            <div v-else class="space-y-2">
              <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                  <polyline points="17 8 12 3 7 8"/>
                  <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
              </div>
              <p class="text-sm font-bold text-gray-600">클릭하거나 파일을 드래그하세요</p>
              <p class="text-xs text-gray-400">JPG, PNG, WEBP · 최대 10MB</p>
            </div>
          </div>

          <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/jpg,image/webp"
            class="hidden" @change="onFileChange"
          />
          <p v-if="errors.image" class="text-red-500 text-xs mt-1">{{ errors.image }}</p>
          <p v-if="banner && !form.image" class="text-xs text-gray-400 mt-1">새 파일을 선택하지 않으면 기존 이미지가 유지됩니다.</p>
        </div>

        <!-- 링크 URL -->
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">클릭 시 이동 URL</label>
          <input v-model="form.link_url" type="text" placeholder="/events 또는 https://..."
            class="w-full border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-indigo-400"
          />
        </div>

        <!-- 순서 + 활성화 -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">노출 순서</label>
            <input v-model.number="form.sort_order" type="number" min="0" placeholder="0"
              class="w-full border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-indigo-400"
            />
            <p class="text-xs text-gray-400 mt-1">숫자가 낮을수록 먼저 표시</p>
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">활성화</label>
            <label class="flex items-center gap-2 mt-2 cursor-pointer">
              <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600" />
              <span class="text-sm text-gray-700">배너 활성화</span>
            </label>
          </div>
        </div>

        <!-- 노출 기간 -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">시작일 <span class="text-red-500">*</span></label>
            <input v-model="form.started_at" type="date" required
              class="w-full border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-indigo-400"
            />
            <p v-if="errors.started_at" class="text-red-500 text-xs mt-1">{{ errors.started_at }}</p>
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">종료일 <span class="text-red-500">*</span></label>
            <input v-model="form.ended_at" type="date" required
              :min="form.started_at"
              class="w-full border-gray-200 rounded-lg text-sm focus:border-indigo-400 focus:ring-indigo-400"
              :class="dateError ? 'border-red-400 ring-1 ring-red-400' : ''"
            />
            <p v-if="dateError" class="text-red-500 text-xs mt-1">종료일은 시작일 이후여야 합니다.</p>
            <p v-else-if="errors.ended_at" class="text-red-500 text-xs mt-1">{{ errors.ended_at }}</p>
          </div>
        </div>

        <!-- 제출 버튼 -->
        <div class="flex gap-3 pt-2">
          <button type="submit" :disabled="processing"
            class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-indigo-700 transition-colors disabled:opacity-50 flex items-center gap-2">
            <span v-if="processing" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            {{ banner ? '수정 완료' : '배너 등록' }}
          </button>
          <Link :href="route('admin.banners.index')"
            class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-200 transition-colors">
            취소
          </Link>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  banner: { type: Object, default: null },
});

const processing = ref(false);
const errors     = ref({});
const previewUrl = ref(props.banner?.image_url ?? null);

const form = ref({
  title:      props.banner?.title      ?? '',
  subtitle:   props.banner?.subtitle   ?? '',
  image:      null, // File 객체
  link_url:   props.banner?.link_url   ?? '',
  is_active:  props.banner?.is_active  ?? true,
  sort_order: props.banner?.sort_order ?? 0,
  started_at: props.banner?.started_at ? props.banner.started_at.slice(0, 10) : '',
  ended_at:   props.banner?.ended_at   ? props.banner.ended_at.slice(0, 10)   : '',
});

// 종료일이 시작일보다 이전인지 체크
const dateError = computed(() =>
  form.value.started_at && form.value.ended_at &&
  form.value.ended_at < form.value.started_at
);

const onFileChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  form.value.image = file;
  previewUrl.value = URL.createObjectURL(file);
};

const onDrop = (e) => {
  const file = e.dataTransfer.files[0];
  if (!file || !file.type.startsWith('image/')) return;
  form.value.image = file;
  previewUrl.value = URL.createObjectURL(file);
};

const removeImage = () => {
  form.value.image = null;
  previewUrl.value = props.banner?.image_url ?? null;
};

const submit = () => {
  if (dateError.value) return;
  processing.value = true;
  errors.value = {};

  const options = {
    forceFormData: true, // 파일 업로드를 위해 FormData 강제
    onError: (e) => { errors.value = e; processing.value = false; },
    onFinish: () => { processing.value = false; },
  };

  if (props.banner) {
    router.post(route('admin.banners.update', props.banner.id), {
      ...form.value,
      _method: 'PUT',
    }, options);
  } else {
    router.post(route('admin.banners.store'), form.value, options);
  }
};
</script>
