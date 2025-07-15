<script setup>
import AppLayout from "@/layouts/app.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";
import DoughnutChart from "@/components/shared/charts/doughnut-chart.vue";

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
import { Table, TableBody, TableCell, TableRow } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";

const breadcrumbs = [{ label: "Dashboard", url: "/dashboard" }];

const props = defineProps({
  projects: Array,
  openTasks: Array,
  analytics: Array,
});

const getProgress = (project) => {
  if (project.tasks_count === 0) return 0;
  return (project.completed_tasks_count / project.tasks_count) * 100;
};

const statusChartData = computed(() => ({
  labels: Object.keys(props.analytics.taskStatusCount),
  datasets: [
    {
      backgroundColor: ["#F97316", "#3B82F6", "#22C55E"],
      data: Object.values(props.analytics.taskStatusCount),
    },
  ],
}));

const chartOptions = {
  responsive: true,
  maintainAspecRation: false,
  plugins: {
    legend: {
      position: "bottom",
    },
  },
};
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex justify-between items-center mb-6">
      <h2 class="font-semibold text-xl">Dashboard</h2>
    </div>

    <div class="pb-12">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <Card>
          <CardHeader>
            <CardTitle>My Completion Rate</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-4xl font-bold berai-font-mono">{{ analytics.completionRate }}%</p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle>Active Projects</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-4xl font-bold berai-font-mono">{{ analytics.totalProjects }}</p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle>Total Assigned Tasks</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-4xl font-bold berai-font-mono">{{ analytics.totalUserTasks }}</p>
          </CardContent>
        </Card>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
          <div v-if="projects.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Card v-for="project in projects" :key="project.id" class="flex flex-col">
              <CardHeader>
                <CardTitle>{{ project.name }}</CardTitle>
                <CardDescription class="line-clamp-2 h-[40px]">
                  {{ project.description || "No description provided." }}
                </CardDescription>
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

        <div class="lg:col-span-1 space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Tasks by Status</CardTitle>
              <CardDescription>A breakdown of all tasks in your projects.</CardDescription>
            </CardHeader>
            <CardContent class="h-72 flex justify-center py-4">
              <DoughnutChart
                v-if="analytics.totalUserTasks > 0"
                :chart-data="statusChartData"
                :chart-options="chartOptions"
              />
              <div v-else class="flex items-center justify-center h-full text-muted-foreground">
                No task data to display.
              </div>
            </CardContent>
          </Card>

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
    </div>
  </AppLayout>
</template>
