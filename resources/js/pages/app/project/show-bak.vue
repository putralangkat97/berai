<script setup>
import { computed } from "vue";
import AppLayout from "@/layouts/app.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Textarea } from "@/components/ui/textarea";
import { toast, Toaster } from "vue-sonner";

const props = defineProps({
  project: Object,
  taskStatuses: Array,
});

const page = usePage();
const authUser = computed(() => page.props.auth.user);
const canAddMembers = computed(
  () => authUser.value.id === props.project.owner_id,
);

const breadcrumbs = [
  { label: "Projects", url: "/dashboard" },
  { label: "Project Details" },
];

const memberForm = useForm({
  email: "",
});

const addMember = () => {
  memberForm.post(`/project/${props.project.id}/members`, {
    preserveScroll: true,
    onSuccess: () => {
      memberForm.reset();
      toast.success("Member Added", {
        description: "The user has been added to the project.",
      });
    },
    onError: (errors) => {
      toast.error("Error", {
        description: errors.email || "There was an error adding the member.",
      });
    },
  });
};

const taskForm = useForm({
  title: "",
  description: "",
  due_date: "",
  assigned_to_id: null,
});

const addTask = () => {
  taskForm.post(`/task/${props.project.id}/task`, {
    preserveScroll: true,
    onSuccess: () => {
      taskForm.reset();
      toast.success("Task Created", {
        description: "The new task has been added to the project.",
      });
    },
    onError: () => {
      toast.error("Creation Failed", {
        description: "Please check the form for errors and try again.",
      });
    },
  });
};

const updateTaskStatus = (task, newStatus) => {
  router.patch(
    `/task/${task.id}`,
    { status: newStatus },
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success("Status Updated", {
          description: `Task status changed to ${newStatus}.`,
        });
      },
    },
  );
};
</script>

<template>
  <Head :title="project.name" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <Toaster position="top-right" />
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-10">
      <div class="lg:col-span-2 space-y-6">
        <!-- Create Task Card -->
        <Card>
          <CardHeader>
            <CardTitle>Create New Task</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="addTask" class="space-y-4">
              <div class="space-y-2">
                <Label for="title">Task Title</Label>
                <Input
                  id="title"
                  v-model="taskForm.title"
                  placeholder="e.g., Design the homepage"
                />
                <p
                  v-if="taskForm.errors.title"
                  class="text-sm text-destructive"
                >
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
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="due_date">Due Date</Label>
                  <Input
                    id="due_date"
                    v-model="taskForm.due_date"
                    type="date"
                  />
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
                      >
                        {{ member.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                  <p
                    v-if="taskForm.errors.assigned_to_id"
                    class="text-sm text-destructive"
                  >
                    {{ taskForm.errors.assigned_to_id }}
                  </p>
                </div>
              </div>
              <Button type="submit" :disabled="taskForm.processing">
                Add Task
              </Button>
            </form>
          </CardContent>
        </Card>

        <!-- Task List Card -->
        <Card>
          <CardHeader>
            <CardTitle>Project Tasks</CardTitle>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Task</TableHead>
                  <TableHead>Assigned To</TableHead>
                  <TableHead>Due Date</TableHead>
                  <TableHead>Status</TableHead>
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
                      {{ task.due_date || "N/A" }}
                    </TableCell>
                    <TableCell>
                      <Select
                        :model-value="task.status"
                        @update:model-value="
                          (newStatus) => updateTaskStatus(task, newStatus)
                        "
                      >
                        <SelectTrigger class="w-[140px]">
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem
                            v-for="status in taskStatuses"
                            :key="status.value"
                            :value="status.value"
                            >{{ status.label }}</SelectItem
                          >
                        </SelectContent>
                      </Select>
                    </TableCell>
                  </TableRow>
                </template>
                <TableRow v-else>
                  <TableCell
                    colspan="4"
                    class="text-center text-muted-foreground py-8"
                  >
                    No tasks have been created for this project yet.
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>

      <div class="lg:col-span-1 space-y-6">
        <!-- Project Details Card -->
        <Card>
          <CardHeader><CardTitle>Project Details</CardTitle></CardHeader>
          <CardContent>
            <p class="text-muted-foreground">
              {{ project.description || "No description was provided." }}
            </p>
          </CardContent>
        </Card>

        <!-- Project Members Card -->
        <Card>
          <CardHeader><CardTitle>Project Members</CardTitle></CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Role</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="member in project.members" :key="member.id">
                  <TableCell class="font-medium">{{ member.name }}</TableCell>
                  <TableCell
                    ><Badge v-if="member.id === project.owner_id"
                      >Owner</Badge
                    ></TableCell
                  >
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>

        <!-- Add Member Card -->
        <Card v-if="canAddMembers">
          <CardHeader>
            <CardTitle>Add Member</CardTitle>
            <CardDescription
              >Add a user to this project by their email.</CardDescription
            >
          </CardHeader>
          <CardContent>
            <form @submit.prevent="addMember" class="space-y-4">
              <div class="space-y-2">
                <Label for="email">User Email</Label>
                <Input
                  id="email"
                  v-model="memberForm.email"
                  type="email"
                  placeholder="user@example.com"
                />
                <p
                  v-if="memberForm.errors.email"
                  class="text-sm text-destructive"
                >
                  {{ memberForm.errors.email }}
                </p>
              </div>
              <Button
                type="submit"
                class="w-full"
                :disabled="memberForm.processing"
                >Add Member</Button
              >
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
