<script setup>
import Guest from "../layouts/guest.vue";
import { Link } from "@inertiajs/vue3";

defineOptions({ layout: Guest });
</script>

<template>
  <div class="py-4">
    <Link href="/login" class="bg-amber-500 px-4 py-3">Hello</Link>
  </div>
</template>
