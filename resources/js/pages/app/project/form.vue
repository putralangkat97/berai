<script setup>
import AppLayout from "@/layouts/app.vue";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Head, useForm } from "@inertiajs/vue3";

const form = useForm({
  name: "",
  description: "",
});

const submit = () => {
  form.post("/project/create");
};

const breadcrumbs = [
  { label: "Project", url: "/project", subs: [{ label: "Project Form" }] },
];
</script>

<template>
  <Head title="Create Project" />
  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="submit">
          <Card>
            <CardHeader>
              <CardTitle>Project Details</CardTitle>
              <CardDescription>
                Give your new project a name and an optional description to get
                started.
              </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
              <!-- Project Name Input -->
              <div class="space-y-2">
                <Label for="name">Project Name</Label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="e.g., Marketing Campaign"
                  required
                />
                <p v-if="form.errors.name" class="text-sm text-destructive">
                  {{ form.errors.name }}
                </p>
              </div>

              <!-- Project Description Input -->
              <div class="space-y-2">
                <Label for="description">Description (Optional)</Label>
                <Textarea
                  id="description"
                  v-model="form.description"
                  placeholder="Describe the main goal of this project."
                />
                <p
                  v-if="form.errors.description"
                  class="text-sm text-destructive"
                >
                  {{ form.errors.description }}
                </p>
              </div>
            </CardContent>
            <CardFooter>
              <Button type="submit" :disabled="form.processing">
                Create Project
              </Button>
            </CardFooter>
          </Card>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
