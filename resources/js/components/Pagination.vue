<template>
  <nav class="flex items-center justify-between">
    <div class="flex-1 flex justify-between sm:hidden">
      <component :is="links[0].url ? links : 'span'" :href="links[0].url"
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        :class="{ 'cursor-not-allowed opacity-50': !links[0].url }">
        Previous
      </component>
      <component :is="links[links.length - 1].url ? links : 'span'" :href="links[links.length - 1].url"
        class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        :class="{ 'cursor-not-allowed opacity-50': !links[links.length - 1].url }">
        Next
      </component>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <component v-for="(link, index) in links" :key="index" :is="link.url ? link : 'span'" :href="link.url"
            class="relative inline-flex items-center px-2 py-2 text-sm font-medium border" :class="[
              link.active
                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              index === 0 ? 'rounded-l-md' : '',
              index === links.length - 1 ? 'rounded-r-md' : '',
              !link.url ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50'
            ]" v-html="link.label" />
        </nav>
      </div>
    </div>
  </nav>
</template>

<script>
import { defineComponent } from 'vue'
import { Link } from '@inertiajs/vue3'

export default defineComponent({
  components: {
    Link,
  },

  props: {
    links: Array,
  },
})
</script>
