<script setup>
import { useForm } from "@inertiajs/vue3";
import { toast } from "vue-sonner";

import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
} from "@/components/ui/select";

const props = defineProps({
  project: Object,
  members: Array,
  taskPriorities: Array,
  open: Boolean,
});

const emit = defineEmits(["update:open"]);

const form = useForm({
  title: "",
  description: "",
  due_date: "",
  assigned_to_id: null,
  priority: 2,
});

const addTask = () => {
  form.post(`/task/${props.project.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      toast.success("Task Created");
      emit("update:open", false); // Close the dialog on success
    },
    onError: () => toast.error("Creation Failed. Please check the form."),
  });
};
</script>

<template>
  <Dialog :open="open" @update:open="(value) => emit('update:open', value)">
    <DialogContent class="sm:max-w-[625px]">
      <DialogHeader>
        <DialogTitle>Create New Task</DialogTitle>
      </DialogHeader>
      <form @submit.prevent="addTask" class="space-y-4 pt-4">
        <div class="space-y-2">
          <Label for="title">Task Title</Label>
          <Input id="title" v-model="form.title" placeholder="e.g., Design the homepage" />
          <p v-if="form.errors.title" class="text-sm text-destructive">
            {{ form.errors.title }}
          </p>
        </div>
        <div class="space-y-2">
          <Label for="description">Description</Label>
          <Textarea
            id="description"
            v-model="form.description"
            placeholder="Add more details about the task..."
          />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="due_date">Due Date</Label>
            <Input id="due_date" v-model="form.due_date" type="date" />
          </div>
          <div class="space-y-2">
            <Label for="assigned_to_id">Assign To</Label>
            <Select v-model="form.assigned_to_id">
              <SelectTrigger>
                <SelectValue placeholder="Select a member" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="member in project.members" :key="member.id" :value="member.id">
                  {{ member.name }}
                </SelectItem>
              </SelectContent>
            </Select>
            <p v-if="form.errors.assigned_to_id" class="text-sm text-destructive">
              {{ form.errors.assigned_to_id }}
            </p>
          </div>
          <div class="space-y-2">
            <Label for="priority">Priority</Label>
            <Select v-model="form.priority">
              <SelectTrigger>
                <SelectValue placeholder="Select priority" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="priority in taskPriorities"
                  :key="priority.value"
                  :value="priority.value"
                >
                  {{ priority.label }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
        <Button type="submit" :disabled="form.processing">Add Task</Button>
      </form>
    </DialogContent>
  </Dialog>
</template>
