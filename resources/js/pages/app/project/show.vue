<script setup>
import AppLayout from "@/layouts/app.vue";
import { computed, ref, reactive, watch } from "vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { toast, Toaster } from "vue-sonner";
import debounce from "lodash.debounce";
import { format, parseISO, formatDistanceToNow } from "date-fns";
import { VueDraggable } from "vue-draggable-plus";
import { List, Kanban } from "lucide-vue-next";

import { Badge } from "@/components/ui/badge";
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
import { Label } from "@/components/ui/label";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Textarea } from "@/components/ui/textarea";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Avatar, AvatarImage, AvatarFallback } from "@/components/ui/avatar";

const props = defineProps({
  project: Object,
  activities: Array,
  taskStatuses: Array,
  taskPriorities: Array,
  filters: Object,
});

const breadcrumbs = [{ label: "Projects", url: "/project", subs: [{ label: "Project Details" }] }];

const page = usePage();
const authUser = computed(() => page.props.auth.user);
const canAddMembers = computed(() => authUser.value.id === props.project.owner_id);

const isAddTaskDialogOpen = ref(false);
const isAddMemberDialogOpen = ref(false);
const viewMode = ref("list");
const isDragging = ref(false);

const todoTasks = ref([]);
const inProgressTasks = ref([]);
const completedTasks = ref([]);

watch(
  () => props.project.tasks,
  (newTasks) => {
    todoTasks.value = newTasks.filter((t) => t.status === 1);
    inProgressTasks.value = newTasks.filter((t) => t.status === 2);
    completedTasks.value = newTasks.filter((t) => t.status === 3);
  },
  {
    immediate: true,
    deep: true,
  }
);

// const todoTasks = computed(() => props.project.tasks.filter((t) => t.status === 1));
// const inProgressTasks = computed(() => props.project.tasks.filter((t) => t.status === 2));
// const completedTasks = computed(() => props.project.tasks.filter((t) => t.status === 3));
watch(
  () => props.project.tasks,
  (newTasks) => {
    todoTasks.value = newTasks.filter((t) => t.status === 1);
    inProgressTasks.value = newTasks.filter((t) => t.status === 2);
    completedTasks.value = newTasks.filter((t) => t.status === 3);
  }
);

const memberForm = useForm({ email: "" });
const taskForm = useForm({
  title: "",
  description: "",
  due_date: "",
  assigned_to_id: null,
  priority: 2,
});

const addMember = () => {
  memberForm.post(`/project/${props.project.id}/members`, {
    preserveScroll: true,
    onSuccess: () => {
      memberForm.reset();
      toast.success("Member Added");
      isAddMemberDialogOpen.value = false;
    },
    onError: (errors) => toast.error(errors.email || "Failed to add member."),
  });
};

const addTask = () => {
  taskForm.post(`/task/${props.project.id}/task`, {
    preserveScroll: true,
    onSuccess: () => {
      taskForm.reset();
      toast.success("Task Created");
      isAddTaskDialogOpen.value = false;
    },
    onError: () => toast.error("Creation Failed. Please check the form."),
  });
};

const updateTask = (task, data) => {
  router.patch(`/task/${task.id}/status`, data, {
    preserveScroll: true,
    onSuccess: (response) => {
      toast.success("Task Updated");
      isDragging.value = false;
    },
    onError: (errors) => {
      console.error("Update failed:", errors);
      toast.error("Failed to update task.");
      isDragging.value = false;

      router.reload({ only: ["project"] });
    },
  });
};

const updateTaskPriority = (task, data) => {
  router.patch(`/task/${task.id}/priority`, data, {
    preserveScroll: true,
    onSuccess: (response) => {
      toast.success("Task Updated");
    },
    onError: (errors) => {
      console.error("Update failed:", errors);
      toast.error("Failed to update task.");
      router.reload({ only: ["project"] });
    },
  });
};

const handleTaskMoved = (event) => {
  const taskId = parseInt(event.item.dataset.taskId);
  const newStatusValue = parseInt(event.to.dataset.statusId);

  if (!taskId || !newStatusValue) {
    console.error("Invalid task ID or status ID");
    return;
  }

  const task = props.project.tasks.find((t) => t.id === taskId);
  if (!task) {
    console.error("Task not found");
    return;
  }

  if (task.status.value !== newStatusValue) {
    updateTask({ id: taskId }, { status: newStatusValue });
  }
};

const onDragStart = (event) => {
  isDragging.value = true;
  event.item.style.opacity = "0.5";
};

const onDragEnd = (event) => {
  event.item.style.opacity = "1";
};

const canMoveTask = (task, newStatus) => {
  if (newStatus === 3 && !authUser.value.can_complete_tasks) {
    return false;
  }
  return true;
};

const dragOptions = {
  animation: 150,
  group: "tasks",
  ghostClass: "ghost",
  chosenClass: "chosen",
  dragClass: "drag",
  disabled: false,
  sort: true,
  delay: 0,
  delayOnTouchOnly: true,
  touchStartThreshold: 5,
  onStart: onDragStart,
  onEnd: onDragEnd,
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

const getPriorityVariant = (priorityValue) => {
  switch (priorityValue) {
    case 4:
      return "destructive";
    case 3:
      return "destructive";
    case 2:
      return "secondary";
    default:
      return "outline";
  }
};

const getPriorityLabel = (priorityValue) => {
  switch (priorityValue) {
    case 4:
      return "Urgent";
    case 3:
      return "High";
    case 2:
      return "Medium";
    default:
      return "Low";
  }
};
</script>

<template>
  <Head :title="project.name" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <Toaster position="top-right" richColors />
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
      <div>
        <h2 class="font-semibold text-2xl">{{ project.name }}</h2>
        <p class="text-muted-foreground mt-1">
          {{ project.description || "No description provided." }}
        </p>
      </div>
      <div class="flex items-center space-x-2 self-start sm:self-center">
        <Dialog v-if="canAddMembers" v-model:open="isAddMemberDialogOpen">
          <DialogTrigger as-child><Button variant="outline">Add Member</Button></DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Add New Member</DialogTitle>
              <DialogDescription>Add a registered user by their email address.</DialogDescription>
            </DialogHeader>
            <form @submit.prevent="addMember" class="space-y-4 pt-4">
              <div class="space-y-2">
                <Label for="add-member-email">User Email</Label>
                <Input
                  id="add-member-email"
                  v-model="memberForm.email"
                  type="email"
                  placeholder="user@example.com"
                />
                <p v-if="memberForm.errors.email" class="text-sm text-destructive">
                  {{ memberForm.errors.email }}
                </p>
              </div>
              <Button type="submit" class="w-full" :disabled="memberForm.processing">
                Add Member
              </Button>
            </form>
          </DialogContent>
        </Dialog>
        <Dialog v-model:open="isAddTaskDialogOpen">
          <DialogTrigger as-child><Button>Create Task</Button></DialogTrigger>
          <DialogContent class="sm:max-w-[625px]">
            <DialogHeader><DialogTitle>Create New Task</DialogTitle></DialogHeader>
            <form @submit.prevent="addTask" class="space-y-4 pt-4">
              <div class="space-y-2">
                <Label for="title">Task Title</Label>
                <Input
                  id="title"
                  v-model="taskForm.title"
                  placeholder="e.g., Design the homepage"
                />
                <p v-if="taskForm.errors.title" class="text-sm text-destructive">
                  {{ taskForm.errors.title }}
                </p>
              </div>
              <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea
                  id="description"
                  v-model="taskForm.description"
                  placeholder="Add more details about the task..."
                />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="due_date">Due Date</Label>
                  <Input id="due_date" v-model="taskForm.due_date" type="date" />
                </div>
                <div class="space-y-2">
                  <Label for="assigned_to_id">Assign To</Label>
                  <Select v-model="taskForm.assigned_to_id">
                    <SelectTrigger>
                      <SelectValue placeholder="Select a member" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem
                        v-for="member in project.members"
                        :key="member.id"
                        :value="member.id"
                        >{{ member.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                  <p v-if="taskForm.errors.assigned_to_id" class="text-sm text-destructive">
                    {{ taskForm.errors.assigned_to_id }}
                  </p>
                </div>
                <div class="space-y-2">
                  <Label for="priority">Priority</Label>
                  <Select v-model="taskForm.priority">
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
              <Button type="submit" :disabled="taskForm.processing">Add Task</Button>
            </form>
          </DialogContent>
        </Dialog>
      </div>
    </div>

    <!-- === TABS === -->
    <Tabs default-value="tasks" class="w-full">
      <TabsList class="grid w-full grid-cols-3">
        <TabsTrigger value="tasks">Tasks</TabsTrigger>
        <TabsTrigger value="members">Members</TabsTrigger>
        <TabsTrigger value="activity">Activity</TabsTrigger>
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
            <div v-if="viewMode === 'list'">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Task</TableHead>
                    <TableHead>Assigned To</TableHead>
                    <TableHead>Due Date</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Priority</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <template v-if="project.tasks.length > 0">
                    <TableRow v-for="task in project.tasks" :key="task.id">
                      <TableCell class="font-medium">{{ task.title }}</TableCell>
                      <TableCell class="text-muted-foreground">
                        {{ task.assigned_user?.name || "Unassigned" }}
                      </TableCell>
                      <TableCell class="text-muted-foreground">
                        {{ formatDate(task.due_date) }}
                      </TableCell>
                      <TableCell>
                        <Select
                          :model-value="task.status"
                          @update:model-value="
                            (newStatus) => updateTask(task, { status: newStatus })
                          "
                        >
                          <SelectTrigger class="w-[140px]">
                            <SelectValue :placeholder="task.status.label" />
                          </SelectTrigger>
                          <SelectContent>
                            <SelectItem value="all">All</SelectItem>
                            <SelectItem
                              v-for="status in taskStatuses"
                              :key="status.value"
                              :value="status.value"
                            >
                              {{ status.label }}
                            </SelectItem>
                          </SelectContent>
                        </Select>
                      </TableCell>
                      <TableCell>
                        <Select
                          :model-value="task.priority"
                          @update:model-value="
                            (newPriority) => updateTaskPriority(task, { priority: newPriority })
                          "
                        >
                          <SelectTrigger class="w-[140px]">
                            <SelectValue :placeholder="task.priority.label" />
                          </SelectTrigger>
                          <SelectContent>
                            <SelectItem value="all">All</SelectItem>
                            <SelectItem
                              v-for="priority in taskPriorities"
                              :key="priority.value"
                              :value="priority.value"
                            >
                              {{ priority.label }}
                            </SelectItem>
                          </SelectContent>
                        </Select>
                      </TableCell>
                    </TableRow>
                  </template>
                  <TableRow v-else>
                    <TableCell colspan="5" class="text-center py-12">
                      No tasks found for the current filters.
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>

            <div v-if="viewMode === 'board'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- To-Do Column -->
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
                    class="cursor-grab"
                  >
                    <Card class="bg-background shadow-sm hover:shadow-md transition-shadow">
                      <CardContent class="p-3">
                        <p class="font-semibold text-sm leading-snug">
                          {{ element.title }}
                        </p>
                        <div class="flex items-center justify-between mt-3">
                          <Badge :variant="getPriorityVariant(element.priority)">
                            {{ getPriorityLabel(element.priority) }}
                          </Badge>
                          <div class="flex items-center gap-2">
                            <span class="text-xs text-muted-foreground">
                              {{ formatDate(element.due_date) }}
                            </span>
                            <Avatar class="h-6 w-6">
                              <AvatarImage
                                :src="
                                  element.assigned_user?.avatar ?? 'https://github.com/unovue.png'
                                "
                              />
                              <AvatarFallback>
                                {{ element.assigned_user?.name.charAt(0) }}
                              </AvatarFallback>
                            </Avatar>
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  </div>
                </VueDraggable>
              </div>

              <!-- In Progress Column -->
              <div class="bg-muted/50 p-4 rounded-lg">
                <h3 class="font-semibold mb-4 text-sm">
                  In Progress · {{ inProgressTasks.length }}
                </h3>
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
                    class="cursor-grab"
                  >
                    <Card class="bg-background shadow-sm hover:shadow-md transition-shadow">
                      <CardContent class="p-3">
                        <p class="font-semibold text-sm leading-snug">
                          {{ element.title }}
                        </p>
                        <div class="flex items-center justify-between mt-3">
                          <Badge :variant="getPriorityVariant(element.priority)">
                            {{ getPriorityLabel(element.priority) }}
                          </Badge>
                          <div class="flex items-center gap-2">
                            <span class="text-xs text-muted-foreground">
                              {{ formatDate(element.due_date) }}
                            </span>
                            <Avatar class="h-6 w-6">
                              <AvatarImage
                                :src="
                                  element.assigned_user?.avatar ?? 'https://github.com/unovue.png'
                                "
                              />
                              <AvatarFallback>
                                {{ element.assigned_user?.name.charAt(0) }}
                              </AvatarFallback>
                            </Avatar>
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  </div>
                </VueDraggable>
              </div>

              <!-- Done Column -->
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
                    class="cursor-grab"
                  >
                    <Card class="bg-background shadow-sm hover:shadow-md transition-shadow">
                      <CardContent class="p-3">
                        <p class="font-semibold text-sm leading-snug">
                          {{ element.title }}
                        </p>
                        <div class="flex items-center justify-between mt-3">
                          <Badge :variant="getPriorityVariant(element.priority)">
                            {{ getPriorityLabel(element.priority) }}
                          </Badge>
                          <div class="flex items-center gap-2">
                            <span class="text-xs text-muted-foreground">
                              {{ formatDate(element.due_date) }}
                            </span>
                            <Avatar class="h-6 w-6">
                              <AvatarImage
                                :src="
                                  element.assigned_user?.avatar ?? 'https://github.com/unovue.png'
                                "
                              />
                              <AvatarFallback>
                                {{ element.assigned_user?.name.charAt(0) }}
                              </AvatarFallback>
                            </Avatar>
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  </div>
                </VueDraggable>
              </div>
            </div>
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
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Email</TableHead>
                  <TableHead>Role</TableHead></TableRow
                >
              </TableHeader>
              <TableBody>
                <TableRow v-for="member in project.members" :key="member.id">
                  <TableCell class="font-medium">{{ member.name }}</TableCell>
                  <TableCell class="text-muted-foreground">{{ member.email }}</TableCell>
                  <TableCell>
                    <Badge v-if="member.id === project.owner_id">Owner</Badge>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
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
            <ul class="space-y-4">
              <li
                v-for="activity in activities"
                :key="activity.id"
                class="flex items-start space-x-3"
              >
                <Avatar class="h-8 w-8">
                  <AvatarImage :src="activity.causer?.avatar ?? 'https://github.com/unovue.png'" />
                  <AvatarFallback>
                    {{ activity.causer?.name.charAt(0) ?? "?" }}
                  </AvatarFallback>
                </Avatar>
                <div class="flex-grow">
                  <p class="text-sm">
                    <span class="font-semibold">{{ activity.causer?.name ?? "A user" }}</span>
                    {{ activity.description }}
                    <span v-if="activity.subject" class="font-semibold">
                      "{{ activity.subject.title }}"
                    </span>
                  </p>
                  <div
                    v-if="activity.properties.old"
                    class="text-xs text-muted-foreground mt-1 pl-4 border-l-2"
                  >
                    <p v-if="activity.properties.old.status">
                      Status changed from
                      <span class="font-medium">
                        {{
                          taskStatuses.find((s) => s.value === activity.properties.old.status).label
                        }}
                      </span>
                      to
                      <span class="font-medium">
                        {{
                          taskStatuses.find(
                            (s) => s.value === activity.properties.attributes.status
                          ).label
                        }}
                      </span>
                    </p>
                    <p v-if="activity.properties.old.priority">
                      Priority changed from
                      <span class="font-medium">
                        {{
                          taskPriorities.find((p) => p.value === activity.properties.old.priority)
                            .label
                        }}
                      </span>
                      to
                      <span class="font-medium">
                        {{
                          taskPriorities.find(
                            (p) => p.value === activity.properties.attributes.priority
                          ).label
                        }}
                      </span>
                    </p>
                  </div>
                  <p class="text-xs text-muted-foreground mt-1">
                    {{ formatDistanceToNow(new Date(activity.created_at), { addSuffix: true }) }}
                  </p>
                </div>
              </li>
              <li
                v-if="!activities || activities.length === 0"
                class="text-center text-muted-foreground py-8"
              >
                No activity recorded yet.
              </li>
            </ul>
          </CardContent>
        </Card>
      </TabsContent>
    </Tabs>
  </AppLayout>
</template>
