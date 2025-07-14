<script setup>
import { toast } from "vue-sonner";
import { useForm } from "@inertiajs/vue3";

import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";

const props = defineProps({
  project: Object,
  open: Boolean,
});

const emit = defineEmits(["update:open"]);

const form = useForm({ email: "" });

const addMember = () => {
  form.post(`/project/${props.project.id}/members`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      toast.success("Member Added");
      emit("update:open", false);
    },
    onError: (errors) => toast.error(errors.email || "Failed to add member."),
  });
};
</script>

<template>
  <Dialog :open="open" @update:open="(value) => emit('update:open', value)">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Add New Member</DialogTitle>
        <DialogDescription> Add a registered user by their email address. </DialogDescription>
      </DialogHeader>
      <form @submit.prevent="addMember" class="space-y-4 pt-4">
        <div class="space-y-2">
          <Label for="add-member-email">User Email</Label>
          <Input
            id="add-member-email"
            v-model="form.email"
            type="email"
            placeholder="user@example.com"
          />
          <p v-if="form.errors.email" class="text-sm text-destructive">
            {{ form.errors.email }}
          </p>
        </div>
        <Button type="submit" class="w-full" :disabled="form.processing"> Add Member </Button>
      </form>
    </DialogContent>
  </Dialog>
</template>
