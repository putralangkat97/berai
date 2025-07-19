<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import { format, parseISO } from "date-fns";
import TaskHelper from "@/helpers/task-helper";
import { Loader } from "lucide-vue-next";

import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from "@/components/ui/dialog";
import { Avatar, AvatarImage, AvatarFallback } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import DateHelper from "@/helpers/date-helper";

const props = defineProps({
  modelValue: Boolean,
  task: Object,
});

const emit = defineEmits(["update:modelValue", "comment-posted"]);

const commentForm = useForm({
  body: "",
});

const submitComment = () => {
  if (!props.task) return;

  commentForm.post(`/task/${props.task.id}/comments`, {
    preserveScroll: true,
    onSuccess: () => {
      commentForm.reset();
      toast.success("Comment posted");
      emit("comment-posted");
    },
    onError: () => toast.error("Failed to post comment."),
  });
};

const user = usePage().props.auth.user;

const taskHelper = new TaskHelper();
const dateHelper = new DateHelper();
</script>

<template>
  <Dialog :open="modelValue" @update:open="(value) => emit('update:modelValue', value)">
    <DialogContent class="sm:max-w-2xl">
      <template v-if="task">
        <DialogHeader>
          <DialogTitle>{{ task.title }}</DialogTitle>
          <DialogDescription>{{ task.description || "No description." }}</DialogDescription>
        </DialogHeader>

        <div class="py-4 space-y-6">
          <!-- Details Section -->
          <div class="grid grid-cols-3 gap-4 text-sm">
            <div>
              <span class="font-semibold mr-2">Status:</span>
              <Badge :variant="taskHelper.getStatusVariant(task.status)">
                {{ taskHelper.getStatusLabel(task.status) }}
              </Badge>
            </div>
            <div>
              <span class="font-semibold mr-2">Priority:</span>
              <Badge :variant="taskHelper.getPriorityVariant(task.priority)">
                {{ taskHelper.getPriorityLabel(task.priority) }}
              </Badge>
            </div>
            <div>
              <span class="font-semibold">Due Date:</span>
              {{ dateHelper.formatDate(task.due_date, "MMM d, yyyy") || "N/A" }}
            </div>
          </div>

          <div class="border-t"></div>

          <!-- Comments Section -->
          <h3 class="font-semibold">Comments</h3>
          <div class="space-y-4">
            <div
              v-for="comment in task.comments"
              :key="comment.id"
              class="flex items-start space-x-3"
            >
              <Avatar class="h-8 w-8">
                <AvatarImage :src="comment?.user?.avatar ?? 'https://github.com/unovue.png'" />
                <AvatarFallback>
                  {{ comment?.user?.name?.charAt(0) }}
                </AvatarFallback>
              </Avatar>
              <div class="flex-1">
                <div class="flex justify-between">
                  <div>
                    <p class="text-sm font-semibold">{{ comment?.user?.name }}</p>
                    <p class="text-sm text-muted-foreground">{{ comment?.body }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-muted-foreground/70">
                      {{ dateHelper.relativeTime(comment?.created_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div
              v-if="!task.comments || task.comments.length === 0"
              class="text-sm text-center text-muted-foreground py-4"
            >
              Be the first to leave a comment.
            </div>
          </div>

          <!-- Add Comment Form -->
          <form @submit.prevent="submitComment" class="flex items-start space-x-3">
            <Avatar class="h-8 w-8">
              <AvatarImage :src="user.avatar ?? 'https://github.com/unovue.png'" />
              <AvatarFallback>
                {{ user.name?.charAt(0) }}
              </AvatarFallback>
            </Avatar>
            <Textarea v-model="commentForm.body" placeholder="Write a comment..." rows="2" />
            <p v-if="commentForm.errors.body" class="text-sm text-destructive">
              {{ commentForm.errors.body }}
            </p>
            <Button type="submit" :disabled="commentForm.processing">
              <Loader v-if="commentForm.processing" class="animate-spin" />
              Post
            </Button>
          </form>
        </div>
      </template>
    </DialogContent>
  </Dialog>
</template>
