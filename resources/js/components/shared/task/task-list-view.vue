<script setup>
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
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
import { Button } from "@/components/ui/button";
import { MoreHorizontal, Pencil, Trash2 } from "lucide-vue-next";
import DateHelper from "@/helpers/date-helper";
import TaskHelper from "@/helpers/task-helper";

const props = defineProps({
  tasks: Array,
  taskStatuses: Array,
  taskPriorities: Array,
});

const emit = defineEmits(["editTask", "deleteTask", "updateTask", "viewTask"]);

const dateHelper = new DateHelper();
const taskHelper = new TaskHelper();
</script>

<template>
  <Table>
    <TableHeader>
      <TableRow>
        <TableHead>Task</TableHead>
        <TableHead>Assigned To</TableHead>
        <TableHead>Due Date</TableHead>
        <TableHead>Status</TableHead>
        <TableHead>Priority</TableHead>
        <TableHead class="text-right">Actions</TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      <template v-if="tasks.length > 0">
        <TableRow v-for="task in tasks" :key="task.id" @click="emit('viewTask', task)">
          <TableCell class="font-medium">{{ task.title }}</TableCell>
          <TableCell class="text-muted-foreground">
            {{ task.assigned_user?.name || "Unassigned" }}
          </TableCell>
          <TableCell class="text-muted-foreground">
            {{ dateHelper.formatDate(task.due_date) }}
          </TableCell>
          <TableCell>
            <Select
              :model-value="task.status.value"
              @update:model-value="(newStatus) => emit('updateTask', task, { status: newStatus })"
            >
              <SelectTrigger class="w-[140px]">
                <SelectValue :placeholder="taskHelper.getStatusLabel(task.status)" />
              </SelectTrigger>
              <SelectContent>
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
              :model-value="task.priority.value"
              @update:model-value="
                (newPriority) => emit('updateTask', task, { priority: newPriority })
              "
            >
              <SelectTrigger class="w-[140px]">
                <SelectValue :placeholder="taskHelper.getPriorityLabel(task.priority)" />
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
          </TableCell>
          <TableCell class="text-right">
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="ghost" size="icon" class="h-8 w-8">
                  <MoreHorizontal class="h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuItem @click="emit('editTask', task)">
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
                      <AlertDialogAction @click="emit('deleteTask', task)">
                        Continue
                      </AlertDialogAction>
                    </AlertDialogFooter>
                  </AlertDialogContent>
                </AlertDialog>
              </DropdownMenuContent>
            </DropdownMenu>
          </TableCell>
        </TableRow>
      </template>
      <TableRow v-else>
        <TableCell colspan="6" class="text-center py-12">
          No tasks found for the current filters.
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>
