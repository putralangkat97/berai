<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
  href: {
    type: String,
    default: "#",
  },
  prefetch: {
    type: Boolean,
    default: true,
  },
});
</script>

<template>
  <Link :href="href" :prefetch="prefetch">
    <slot />
  </Link>
</template>
