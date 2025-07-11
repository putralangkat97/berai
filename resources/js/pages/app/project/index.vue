<script setup>
import AppLayout from "@/layouts/app.vue";
import { Head } from "@inertiajs/vue3";
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Link } from "@inertiajs/vue3";

const breadcrumbs = [{ label: "Project", url: "/project" }];

defineProps({
  projects: Array,
});
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Grid for Project Cards -->
        <div
          v-if="projects.length > 0"
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          <Card v-for="project in projects" :key="project.id">
            <CardHeader>
              <CardTitle>{{ project.name }}</CardTitle>
              <CardDescription class="line-clamp-2 h-[40px]">
                {{ project.description || "No description provided." }}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <!-- We can add progress bars here in a future step -->
              <p class="text-sm text-muted-foreground">
                (Progress bar will go here)
              </p>
            </CardContent>
            <CardFooter>
              <Button as-child class="w-full">
                <Link :href="`/project/show/${project.id}`">View Project</Link>
              </Button>
            </CardFooter>
          </Card>
        </div>

        <!-- State for when no projects exist -->
        <div v-else>
          <Card class="text-center">
            <CardHeader>
              <CardTitle>No Projects Yet</CardTitle>
              <CardDescription>
                Get started by creating your first project.
              </CardDescription>
            </CardHeader>
            <CardContent>
              <Button as-child>
                <Link href="/project/create">Create Your First Project</Link>
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
