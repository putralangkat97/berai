<script setup>
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuPortal,
} from "@/components/ui/dropdown-menu";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { Card, CardContent } from "@/components/ui/card";
import { Avatar, AvatarImage, AvatarFallback } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

import { VueDraggable } from "vue-draggable-plus";
import { MoreHorizontal, Pencil, Trash2 } from "lucide-vue-next";
import DateHelper from "@/helpers/date-helper.js";
import TaskHelper from "@/helpers/task-helper.js";
import { watch, ref } from "vue";

const props = defineProps({
  tasks: Array,
});

const emit = defineEmits(["taskMoved", "editTask", "deleteTask", "viewTask"]);

const todoTasks = ref([]);
const inProgressTasks = ref([]);
const completedTasks = ref([]);

const TODO = 1;
const IN_PROGRESS = 2;
const COMPLETED = 3;

watch(
  () => props.tasks,
  (newTasks) => {
    todoTasks.value = newTasks.filter((t) => t.status === TODO);
    inProgressTasks.value = newTasks.filter((t) => t.status === IN_PROGRESS);
    completedTasks.value = newTasks.filter((t) => t.status === COMPLETED);
  },
  { immediate: true, deep: true }
);

const handleTaskMoved = (event) => {
  const taskId = event.item.dataset.taskId;
  const newStatus = event.to.dataset.statusId;
  emit("taskMoved", { taskId, newStatus });
};

const dateHelper = new DateHelper();
const taskHelper = new TaskHelper();
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- to-do -->
    <div class="bg-muted/50 p-4 rounded-lg">
      <h3 class="font-semibold mb-4 text-sm">To-Do · {{ todoTasks.length }}</h3>
      <VueDraggable
        v-model="todoTasks"
        group="tasks"
        class="space-y-3 min-h-[100px]"
        @end="handleTaskMoved"
        :data-status-id="1"
      >
        <div
          v-for="element in todoTasks"
          :key="element.id"
          :data-task-id="element.id"
          class="cursor-grab group relative"
        >
          <Card
            class="bg-background shadow-sm hover:shadow-md transition-shadow"
            @click="emit('viewTask', element)"
          >
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button
                  variant="ghost"
                  size="icon"
                  class="h-6 w-6 absolute top-1 right-1 hidden group-hover:inline-flex"
                >
                  <MoreHorizontal class="h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuPortal>
                <DropdownMenuContent>
                  <DropdownMenuItem @click="emit('editTask', element)">
                    <Pencil class="mr-2 h-4 w-4" />Edit
                  </DropdownMenuItem>
                  <AlertDialog>
                    <AlertDialogTrigger as-child>
                      <DropdownMenuItem @select.prevent class="text-destructive">
                        <Trash2 class="mr-2 h-4 w-4" />Delete
                      </DropdownMenuItem>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                      <AlertDialogHeader>
                        <AlertDialogTitle>Delete Task?</AlertDialogTitle>
                        <AlertDialogDescription>
                          This action cannot be undone.
                        </AlertDialogDescription>
                      </AlertDialogHeader>
                      <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <AlertDialogAction @click="emit('deleteTask', element)">
                          Continue
                        </AlertDialogAction>
                      </AlertDialogFooter>
                    </AlertDialogContent>
                  </AlertDialog>
                </DropdownMenuContent>
              </DropdownMenuPortal>
            </DropdownMenu>
            <CardContent class="p-3">
              <p class="font-semibold text-sm leading-snug">{{ element.title }}</p>
              <div class="flex items-center justify-between mt-3">
                <Badge :variant="taskHelper.getPriorityVariant(element.priority)">
                  {{ taskHelper.getPriorityLabel(element.priority) }}
                </Badge>
                <div class="flex items-center gap-2">
                  <span class="text-xs text-muted-foreground">
                    {{ dateHelper.formatDate(element.due_date) }}
                  </span>
                  <Avatar class="h-6 w-6">
                    <AvatarImage
                      :src="element.assigned_user?.avatar ?? 'https://github.com/unovue.png'"
                    />
                    <AvatarFallback>
                      {{ element.assigned_user?.name.charAt(0) ?? "U" }}
                    </AvatarFallback>
                  </Avatar>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </VueDraggable>
    </div>
    <!-- end to-do -->

    <!-- in progress -->
    <div class="bg-muted/50 p-4 rounded-lg">
      <h3 class="font-semibold mb-4 text-sm">In Progress · {{ inProgressTasks.length }}</h3>
      <VueDraggable
        v-model="inProgressTasks"
        group="tasks"
        class="space-y-3 min-h-[100px]"
        @end="handleTaskMoved"
        :data-status-id="2"
      >
        <div
          v-for="element in inProgressTasks"
          :key="element.id"
          :data-task-id="element.id"
          class="cursor-grab group relative"
        >
          <Card
            class="bg-background shadow-sm hover:shadow-md transition-shadow"
            @click="emit('viewTask', element)"
          >
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button
                  variant="ghost"
                  size="icon"
                  class="h-6 w-6 absolute top-1 right-1 hidden group-hover:inline-flex"
                >
                  <MoreHorizontal class="h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuPortal>
                <DropdownMenuContent>
                  <DropdownMenuItem @click="emit('editTask', element)">
                    <Pencil class="mr-2 h-4 w-4" />Edit
                  </DropdownMenuItem>
                  <AlertDialog>
                    <AlertDialogTrigger as-child>
                      <DropdownMenuItem @select.prevent class="text-destructive">
                        <Trash2 class="mr-2 h-4 w-4" />Delete
                      </DropdownMenuItem>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                      <AlertDialogHeader>
                        <AlertDialogTitle>Delete Task?</AlertDialogTitle>
                        <AlertDialogDescription>
                          This action cannot be undone.
                        </AlertDialogDescription>
                      </AlertDialogHeader>
                      <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <AlertDialogAction @click="emit('deleteTask', element)">
                          Continue
                        </AlertDialogAction>
                      </AlertDialogFooter>
                    </AlertDialogContent>
                  </AlertDialog>
                </DropdownMenuContent>
              </DropdownMenuPortal>
            </DropdownMenu>
            <CardContent class="p-3">
              <p class="font-semibold text-sm leading-snug">{{ element.title }}</p>
              <div class="flex items-center justify-between mt-3">
                <Badge :variant="taskHelper.getPriorityVariant(element.priority)">
                  {{ taskHelper.getPriorityLabel(element.priority) }}
                </Badge>
                <div class="flex items-center gap-2">
                  <span class="text-xs text-muted-foreground">
                    {{ dateHelper.formatDate(element.due_date) }}
                  </span>
                  <Avatar class="h-6 w-6">
                    <AvatarImage
                      :src="element.assigned_user?.avatar ?? 'https://github.com/unovue.png'"
                    />
                    <AvatarFallback>
                      {{ element.assigned_user?.name.charAt(0) ?? "U" }}
                    </AvatarFallback>
                  </Avatar>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </VueDraggable>
    </div>
    <!-- end in progress -->

    <!-- done -->
    <div class="bg-muted/50 p-4 rounded-lg">
      <h3 class="font-semibold mb-4 text-sm">Done · {{ completedTasks.length }}</h3>
      <VueDraggable
        v-model="completedTasks"
        group="tasks"
        class="space-y-3 min-h-[100px]"
        @end="handleTaskMoved"
        :data-status-id="3"
      >
        <div
          v-for="element in completedTasks"
          :key="element.id"
          :data-task-id="element.id"
          class="cursor-grab group relative"
        >
          <Card
            class="bg-background shadow-sm opacity-70 hover:shadow-md transition-shadow"
            @click="emit('viewTask', element)"
          >
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button
                  variant="ghost"
                  size="icon"
                  class="h-6 w-6 absolute top-1 right-1 hidden group-hover:inline-flex"
                >
                  <MoreHorizontal class="h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuPortal>
                <DropdownMenuContent>
                  <DropdownMenuItem @click="emit('editTask', element)">
                    <Pencil class="mr-2 h-4 w-4" />Edit
                  </DropdownMenuItem>
                  <AlertDialog>
                    <AlertDialogTrigger as-child>
                      <DropdownMenuItem @select.prevent class="text-destructive">
                        <Trash2 class="mr-2 h-4 w-4" />Delete
                      </DropdownMenuItem>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                      <AlertDialogHeader>
                        <AlertDialogTitle>Delete Task?</AlertDialogTitle>
                        <AlertDialogDescription>
                          This action cannot be undone.
                        </AlertDialogDescription>
                      </AlertDialogHeader>
                      <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <AlertDialogAction @click="emit('deleteTask', element)">
                          Continue
                        </AlertDialogAction>
                      </AlertDialogFooter>
                    </AlertDialogContent>
                  </AlertDialog>
                </DropdownMenuContent>
              </DropdownMenuPortal>
            </DropdownMenu>
            <CardContent class="p-3">
              <p class="font-semibold text-sm leading-snug line-through">{{ element.title }}</p>
              <div class="flex items-center justify-between mt-3">
                <Badge :variant="taskHelper.getPriorityVariant(element.priority)">
                  {{ taskHelper.getPriorityLabel(element.priority) }}
                </Badge>
                <div class="flex items-center gap-2">
                  <span class="text-xs text-muted-foreground">
                    {{ dateHelper.formatDate(element.due_date) }}
                  </span>
                  <Avatar class="h-6 w-6">
                    <AvatarImage
                      :src="element.assigned_user?.avatar ?? 'https://github.com/unovue.png'"
                    />
                    <AvatarFallback>
                      {{ element.assigned_user?.name.charAt(0) ?? "U" }}
                    </AvatarFallback>
                  </Avatar>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </VueDraggable>
    </div>
    <!-- end done -->
  </div>
</template>
