<script setup>
import { watch, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";

const props = defineProps({
  modelValue: Boolean,
  task: Object,
  members: Array,
  taskPriorities: Array,
});

const emit = defineEmits(["update:modelValue", "submit"]);

const form = useForm({
  title: "",
  description: "",
  due_date: "",
  assigned_to_id: null,
  priority: null,
});

watch(
  () => props.task,
  (newTask) => {
    if (newTask) {
      form.title = newTask.title;
      form.description = newTask.description;
      form.due_date = newTask.due_date;
      form.assigned_to_id = newTask.assigned_user?.id || null;
      form.priority = newTask.priority.value;
    }
  },
  { immediate: true }
);

const submit = () => {
  emit("submit", form);
};
</script>

<template>
  <Dialog :open="modelValue" @update:open="(value) => emit('update:modelValue', value)">
    <DialogContent class="sm:max-w-[625px]">
      <DialogHeader>
        <DialogTitle>Edit Task</DialogTitle>
      </DialogHeader>
      <form @submit.prevent="submit" class="space-y-4 pt-4">
        <div class="space-y-2">
          <Label for="edit-title">Task Title</Label>
          <Input id="edit-title" v-model="form.title" />
        </div>
        <div class="space-y-2">
          <Label for="edit-description">Description</Label>
          <Textarea id="edit-description" v-model="form.description" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="edit-due_date">Due Date</Label>
            <Input id="edit-due_date" v-model="form.due_date" type="date" />
          </div>
          <div class="space-y-2">
            <Label for="edit-assigned_to_id">Assign To</Label>
            <Select v-model="form.assigned_to_id">
              <SelectTrigger>
                <SelectValue placeholder="Select a member" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="member in members" :key="member.id" :value="member.id">
                  {{ member.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div class="space-y-2">
            <Label for="edit-priority">Priority</Label>
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
        <Button type="submit" :disabled="form.processing">Save Changes</Button>
      </form>
    </DialogContent>
  </Dialog>
</template>
