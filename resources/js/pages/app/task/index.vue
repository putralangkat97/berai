<script setup>
import AppLayout from "@/layouts/app.vue";
import { Head, router } from "@inertiajs/vue3";
import { watch, reactive } from "vue";
import { format, parseISO } from "date-fns";
import debounce from "lodash.debounce";
import TaskHelper from "@/helpers/task-helper";

import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

const breadcrumbs = [{ label: "My Tasks", url: "/task" }];

const props = defineProps({
  tasks: Array,
  filters: Object,
  taskStatuses: Array,
  taskPriorities: Array,
});

const filters = reactive({
  search: props.filters.search || "",
  status: props.filters.status || "",
  priority: props.filters.priority || "",
});

watch(
  filters,
  debounce((newFilters) => {
    router.get(
      "/task",
      {
        search: newFilters.search,
        status: newFilters.status,
        priority: newFilters.priority,
      },
      {
        preserveState: true,
        replace: true,
      }
    );
  }, 500),
  { deep: true }
);

const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  try {
    return format(parseISO(dateString), "MMM d, yyyy");
  } catch (e) {
    return dateString;
  }
};

const taskHelper = new TaskHelper();
</script>

<template>
  <Head title="My Tasks" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex justify-between items-center mb-6">
      <h2 class="font-semibold text-xl">My Tasks</h2>
    </div>

    <Card class="mb-6">
      <CardContent class="pt-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Input v-model="filters.search" placeholder="Search by task title..." />
          <Select v-model="filters.status">
            <SelectTrigger>
              <SelectValue placeholder="Filter by status" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Statuses</SelectItem>
              <SelectItem v-for="status in taskStatuses" :key="status.label" :value="status.value">
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
                :key="priority.label"
                :value="priority.value"
              >
                {{ priority.label }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>
      </CardContent>
    </Card>

    <Card>
      <CardHeader>
        <CardTitle>All Assigned Tasks</CardTitle>
      </CardHeader>
      <CardContent>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Status</TableHead>
              <TableHead>Task</TableHead>
              <TableHead>Project</TableHead>
              <TableHead>Due Date</TableHead>
              <TableHead>Priority</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="tasks.length > 0">
              <TableRow
                v-for="task in tasks"
                :key="task.id"
                class="hover:bg-muted/90 cursor-pointer"
                @click="router.get(`/project/${task.project.id}`)"
              >
                <TableCell>
                  <Badge
                    :variant="taskHelper.getStatusVariant(task.status)"
                    class="berai-font-mono"
                  >
                    {{ taskHelper.getStatusLabel(task.status) }}
                  </Badge>
                </TableCell>
                <TableCell class="font-medium">{{ task.title }}</TableCell>
                <TableCell class="text-muted-foreground">
                  {{ task.project.name }}
                </TableCell>
                <TableCell>{{ formatDate(task.due_date) }}</TableCell>
                <TableCell>
                  <Badge
                    :variant="taskHelper.getPriorityVariant(task.priority)"
                    class="berai-font-mono"
                  >
                    {{ taskHelper.getPriorityLabel(task.priority) }}
                  </Badge>
                </TableCell>
              </TableRow>
            </template>
            <TableRow v-else>
              <TableCell colspan="4" class="text-center text-muted-foreground py-12">
                No tasks match your current filters.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  </AppLayout>
</template>
