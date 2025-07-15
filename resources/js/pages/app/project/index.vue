<script setup>
import AppLayout from "@/layouts/app.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { watch, reactive } from "vue";
import debounce from "lodash.debounce";

import { Card, CardHeader, CardTitle, CardContent, CardFooter } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Progress } from "@/components/ui/progress";
import { Input } from "@/components/ui/input";

const breadcrumbs = [{ label: "Project", url: "/project" }];

const props = defineProps({
  projects: Array,
  filters: Object,
});

const filters = reactive({
  search: props.filters.search || "",
});

const getProgress = (project) => {
  if (project.tasks_count === 0) return 0;
  return (project.completed_tasks_count / project.tasks_count) * 100;
};

watch(
  filters,
  debounce((newFilters) => {
    router.get(
      "/project",
      { search: newFilters.search },
      {
        preserveState: true,
        replace: true,
      }
    );
  }, 500)
);
</script>

<template>
  <Head title="Projects" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
      <h2 class="font-semibold text-xl">Projects</h2>
      <div class="flex space-x-2">
        <div class="w-full sm:w-auto sm:max-w-xs">
          <Input v-model="filters.search" type="text" placeholder="Search projects..." />
        </div>
        <Button as-child>
          <Link href="/project/create">Create Project</Link>
        </Button>
      </div>
    </div>

    <!-- Project Cards -->
    <div v-if="projects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <Card v-for="project in projects" :key="project.id" class="flex flex-col">
        <CardHeader>
          <CardTitle>{{ project.name }}</CardTitle>
        </CardHeader>
        <CardContent class="flex-grow">
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-muted-foreground">Progress</span>
            <span class="text-sm font-medium"
              >{{ project.completed_tasks_count }} / {{ project.tasks_count }} tasks</span
            >
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

    <!-- empty state when no projects exists -->
    <div v-else>
      <div class="text-center border-2 border-dashed rounded-lg p-12">
        <svg
          class="mx-auto h-12 w-12 text-muted-foreground"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          aria-hidden="true"
        >
          <path
            vector-effect="non-scaling-stroke"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
          />
        </svg>
        <h3 class="mt-2 text-sm font-semibold">No projects</h3>
        <p class="mt-1 text-sm text-muted-foreground">Get started by creating a new project.</p>
        <div class="mt-6">
          <Button as-child>
            <Link href="/project/create">
              <Plus class="mr-2 h-4 w-4" />
              Create Project
            </Link>
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
