<script setup>
import AppLayout from "@/layouts/app.vue";
import { Head, Link } from "@inertiajs/vue3";

import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Progress } from "@/components/ui/progress";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";

const breadcrumbs = [{ label: "Dashboard", url: "/dashboard" }];

defineProps({
  projects: Array,
  openTasks: Array,
});

const getProgress = (project) => {
  if (project.tasks_count === 0) return 0;
  return (project.completed_tasks_count / project.tasks_count) * 100;
};
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex justify-between items-center mb-6">
      <h2 class="font-semibold text-xl">Dashboard</h2>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <div v-if="projects.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <Card v-for="project in projects" :key="project.id" class="flex flex-col">
            <CardHeader>
              <CardTitle>{{ project.name }}</CardTitle>
              <CardDescription class="line-clamp-2 h-[40px]">{{
                project.description || "No description provided."
              }}</CardDescription>
            </CardHeader>
            <CardContent class="flex-grow">
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-muted-foreground">Progress</span>
                <span class="text-sm font-medium">
                  {{ project.completed_tasks_count }} / {{ project.tasks_count }} tasks
                </span>
              </div>
              <Progress :model-value="getProgress(project)" />
            </CardContent>
            <CardFooter>
              <Button as-child variant="secondary" class="w-full">
                <Link :href="`/project/${project.id}`">View Project</Link>
              </Button>
            </CardFooter>
          </Card>
        </div>
        <div v-else>
          <Card class="text-center">
            <CardHeader>
              <CardTitle>Welcome to Berai!</CardTitle>
              <CardDescription> Get started by creating your first project. </CardDescription>
            </CardHeader>
            <CardContent>
              <Button size="lg" as-child class="mt-1">
                <Link href="/project/create"> Create Your First Project </Link>
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>

      <div class="lg:col-span-1">
        <Card>
          <CardHeader>
            <CardTitle>My Open Tasks</CardTitle>
            <CardDescription>Your most urgent tasks across all projects.</CardDescription>
          </CardHeader>
          <CardContent>
            <Table>
              <TableBody>
                <template v-if="openTasks.length > 0">
                  <Link
                    v-for="task in openTasks"
                    :key="task.id"
                    :href="`/project/${task.project.id}`"
                    as="tr"
                    class="hover:bg-muted/50 cursor-pointer"
                  >
                    <TableCell>
                      <div class="font-medium">{{ task.title }}</div>
                      <div class="text-xs text-muted-foreground">
                        {{ task.project.name }}
                      </div>
                    </TableCell>
                    <TableCell class="text-right">
                      <Badge variant="destructive">{{ task.due_date || "No due date" }}</Badge>
                    </TableCell>
                  </Link>
                </template>
                <TableRow v-else>
                  <TableCell class="text-center py-12">
                    You have no open tasks. Great job!
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
          <CardFooter v-if="openTasks.length > 0">
            <Button as-child variant="outline" class="w-full">
              <Link href="/task">View All My Tasks</Link>
            </Button>
          </CardFooter>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
