<script setup>
import { GalleryVerticalEnd } from "lucide-vue-next";
import LoginForm from "@/components/auth/login-form.vue";
import GuestLayout from "@/layouts/guest.vue";
</script>

<template>
  <GuestLayout>
    <a href="#" class="flex items-center gap-2 self-center font-medium">
      <div
        class="flex h-6 w-6 items-center justify-center rounded-md bg-primary text-primary-foreground"
      >
        <GalleryVerticalEnd class="size-4" />
      </div>
      Acme Inc.
    </a>
    <LoginForm />
  </GuestLayout>
</template>
