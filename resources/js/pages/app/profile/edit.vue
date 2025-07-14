<script setup>
import AppLayout from "@/layouts/app.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import { toast, Toaster } from "vue-sonner";

import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

const user = usePage().props.auth.user;

const form = useForm({
  _method: "POST",
  name: user.name,
  email: user.email,
  avatar: null,
});

const avatarPreview = ref(user.avatar ? `/storage/${user.avatar}` : null);

function handleAvatarChange(event) {
  const file = event.target.files[0];
  if (file) {
    form.avatar = file;
    avatarPreview.value = URL.createObjectURL(file);
  }
}

function submit() {
  form.post("/profile", {
    onSuccess: () => {
      toast.success("Profile updated successfully.");
    },
  });
}
</script>

<template>
  <Head title="My Profile" />
  <AppLayout>
    <Toaster position="top-right" richColors />
    <h2 class="font-semibold text-xl">My Profile</h2>

    <div class="max-w-2xl mx-auto py-12">
      <Card>
        <CardHeader
          ><CardTitle>Profile Information</CardTitle
          ><CardDescription>Update your account's profile information.</CardDescription></CardHeader
        >
        <form @submit.prevent="submit">
          <CardContent class="space-y-6">
            <!-- Avatar Input -->
            <div class="space-y-2">
              <Label>Avatar</Label>
              <div class="flex items-center gap-4">
                <img
                  v-if="avatarPreview"
                  :src="avatarPreview"
                  class="w-20 h-20 rounded-full object-cover"
                />
                <div
                  v-else
                  class="w-20 h-20 rounded-full bg-muted flex items-center justify-center"
                >
                  ...
                </div>
                <input
                  type="file"
                  @input="handleAvatarChange"
                  class="file-input file-input-bordered"
                />
              </div>
              <p v-if="form.errors.avatar" class="text-sm text-destructive">
                {{ form.errors.avatar }}
              </p>
            </div>

            <!-- Name & Email Inputs -->
            <div class="space-y-2">
              <Label for="name">Name</Label><Input id="name" v-model="form.name" />
            </div>
            <div class="space-y-2">
              <Label for="email">Email</Label><Input id="email" v-model="form.email" />
            </div>
          </CardContent>
          <CardFooter>
            <Button type="submit" :disabled="form.processing" class="mt-6">Save Changes</Button>
          </CardFooter>
        </form>
      </Card>
    </div>
  </AppLayout>
</template>
