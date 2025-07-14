<script setup>
import AppLayout from "@/layouts/app.vue";
import { computed, ref, reactive, watch } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { toast, Toaster } from "vue-sonner";
import debounce from "lodash.debounce";

// --- ICONS & UI ---
import { List, Kanban } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";

// --- PARTIALS ---
import AddMemberDialog from "@/components/shared/project/add-member-dialog.vue";
import MembersList from "@/components/shared/project/member-list-view.vue";
import ActivityFeed from "@/components/shared/project/activity-feed.vue";
import SettingsTab from "@/components/shared/project/setting-tab.vue";
import CreateTaskDialog from "@/components/shared/task/create-task-dialog.vue";
import EditTaskDialog from "@/components/shared/task/edit-task-dialog.vue";
import TaskListView from "@/components/shared/task/task-list-view.vue";
import TaskBoardView from "@/components/shared/task/task-board-view.vue";

// --- PROPS & PAGE DATA ---
const props = defineProps({
  project: Object,
  activities: Array,
  taskStatuses: Array,
  taskPriorities: Array,
  filters: Object,
});

const page = usePage();
const authUser = computed(() => page.props.auth.user);
const canManageProject = computed(() => authUser.value.id === props.project.owner_id);

// --- COMPONENT STATE ---
const isAddTaskDialogOpen = ref(false);
const isAddMemberDialogOpen = ref(false);
const isEditTaskDialogOpen = ref(false);
const editingTask = ref(null);
const viewMode = ref("list");

// --- ACTIONS ---
const updateTask = (task, data) => {
  router.patch(`/task/${task.id}`, data, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success("Task Updated");
      if (isEditTaskDialogOpen.value) {
        isEditTaskDialogOpen.value = false;
      }
    },
    onError: () => toast.error("Failed to update task."),
  });
};

const handleTaskMoved = ({ taskId, newStatus }) => {
  updateTask({ id: taskId }, { status: newStatus });
};

const openEditTaskDialog = (task) => {
  editingTask.value = task;
  isEditTaskDialogOpen.value = true;
};

const deleteTask = (task) => {
  router.delete(`/tasks/${task.id}`, {
    preserveScroll: true,
    onSuccess: () => toast.success("Task Deleted"),
  });
};

const filters = reactive({
  search: props.filters.search || "",
  status: props.filters.status || "",
  priority: props.filters.priority || "",
});

watch(
  filters,
  debounce((newFilters) => {
    router.get(`/project/${props.project.id}`, newFilters, {
      preserveState: true,
      replace: true,
      preserveScroll: true,
    });
  }, 500)
);

const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  try {
    return format(parseISO(dateString), "MMM d");
  } catch (e) {
    return dateString;
  }
};

const breadcrumbs = [{ label: "Project", url: "/project", subs: [{ label: "Project Details" }] }];
</script>

<template>
  <Head :title="project.name" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <Toaster position="top-right" richColors />

    <!-- === DIALOGS === -->
    <CreateTaskDialog
      v-model:open="isAddTaskDialogOpen"
      :project="project"
      :members="project.members"
      :task-priorities="taskPriorities"
    />
    <EditTaskDialog
      v-model="isEditTaskDialogOpen"
      :task="editingTask"
      :members="project.members"
      :task-priorities="taskPriorities"
      @submit="(form) => updateTask(editingTask, form.data())"
    />
    <AddMemberDialog
      v-model:open="isAddMemberDialogOpen"
      :project="project"
      v-if="canManageProject"
    />

    <!-- === HEADER === -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
      <div>
        <h2 class="font-semibold text-2xl">{{ project.name }}</h2>
        <p class="text-muted-foreground mt-1">
          {{ project.description || "No description provided." }}
        </p>
      </div>
      <div class="flex items-center space-x-2 self-start sm:self-center">
        <Button v-if="canManageProject" variant="outline" @click="isAddMemberDialogOpen = true">
          Add Member
        </Button>
        <Button @click="isAddTaskDialogOpen = true">Create Task</Button>
      </div>
    </div>

    <!-- === TABS === -->
    <Tabs default-value="tasks" class="w-full">
      <TabsList class="grid w-full" :class="canManageProject ? 'grid-cols-4' : 'grid-cols-3'">
        <TabsTrigger value="tasks">Tasks</TabsTrigger>
        <TabsTrigger value="members">Members</TabsTrigger>
        <TabsTrigger value="activity">Activity</TabsTrigger>
        <TabsTrigger v-if="canManageProject" value="settings">Settings</TabsTrigger>
      </TabsList>

      <!-- === TASKS TAB === -->
      <TabsContent value="tasks" class="mt-4">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Project Tasks</CardTitle>
              <CardDescription>View tasks as a list or a Kanban board.</CardDescription>
            </div>
            <div class="flex items-center rounded-md bg-secondary text-secondary-foreground p-1">
              <Button
                variant="ghost"
                size="sm"
                @click="viewMode = 'list'"
                :class="{ 'bg-background text-primary': viewMode === 'list' }"
                class="h-8 px-3"
              >
                <List class="h-4 w-4" />
              </Button>
              <Button
                variant="ghost"
                size="sm"
                @click="viewMode = 'board'"
                :class="{ 'bg-background text-primary': viewMode === 'board' }"
                class="h-8 px-3"
              >
                <Kanban class="h-4 w-4" />
              </Button>
            </div>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 border-b pb-6">
              <Input v-model="filters.search" placeholder="Search task titles..." />
              <Select v-model="filters.status">
                <SelectTrigger>
                  <SelectValue placeholder="Filter by status" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Statuses</SelectItem>
                  <SelectItem
                    v-for="status in taskStatuses"
                    :key="status.value"
                    :value="status.value"
                  >
                    {{ status.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <Select v-model="filters.priority">
                <SelectTrigger>
                  <SelectValue placeholder="Filter by priority" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Priorities</SelectItem>
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
            <TaskListView
              v-if="viewMode === 'list'"
              :tasks="project.tasks"
              :task-statuses="taskStatuses"
              :task-priorities="taskPriorities"
              @edit-task="openEditTaskDialog"
              @delete-task="deleteTask"
              @update-task="updateTask"
            />
            <TaskBoardView
              v-else-if="viewMode === 'board'"
              :tasks="project.tasks"
              :task-priorities="taskPriorities"
              @edit-task="openEditTaskDialog"
              @delete-task="deleteTask"
              @task-moved="handleTaskMoved"
            />
          </CardContent>
        </Card>
      </TabsContent>

      <!-- === MEMBERS TAB === -->
      <TabsContent value="members" class="mt-4">
        <Card>
          <CardHeader>
            <CardTitle>Project Members</CardTitle>
            <CardDescription>Users who have access to this project.</CardDescription>
          </CardHeader>
          <CardContent>
            <MembersList :members="project.members" :owner-id="project.owner_id" />
          </CardContent>
        </Card>
      </TabsContent>

      <!-- === ACTIVITY TAB === -->
      <TabsContent value="activity" class="mt-4">
        <Card>
          <CardHeader>
            <CardTitle>Recent Activity</CardTitle>
          </CardHeader>
          <CardContent>
            <ActivityFeed
              :activities="activities"
              :task-statuses="taskStatuses"
              :task-priorities="taskPriorities"
            />
          </CardContent>
        </Card>
      </TabsContent>

      <!-- === SETTINGS TAB === -->
      <TabsContent v-if="canManageProject" value="settings" class="mt-4">
        <SettingsTab :project="project" />
      </TabsContent>
    </Tabs>
  </AppLayout>
</template>
