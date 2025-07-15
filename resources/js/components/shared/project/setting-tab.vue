<script setup>
import { useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
  CardFooter,
} from "@/components/ui/card";
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
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";

const props = defineProps({
  project: Object,
});

const form = useForm({
  name: props.project.name,
  description: props.project.description,
});

const updateProject = () => {
  form.put(`/project/${props.project.id}`, {
    preserveScroll: true,
    onSuccess: () => toast.success("Project Updated"),
  });
};

const deleteProject = () => {
  router.delete(`/project/${props.project.id}`);
};
</script>
<template>
  <div class="space-y-6">
    <Card>
      <CardHeader>
        <CardTitle>Project Settings</CardTitle>
        <CardDescription>Update your project's name and description.</CardDescription>
      </CardHeader>
      <form @submit.prevent="updateProject">
        <CardContent class="space-y-4 pt-4">
          <div class="space-y-2">
            <Label for="project-name">Project Name</Label>
            <Input id="project-name" v-model="form.name" type="text" required />
          </div>
          <div class="space-y-2">
            <Label for="project-description">Description</Label>
            <Textarea id="project-description" v-model="form.description" />
          </div>
        </CardContent>
        <CardFooter>
          <Button type="submit" :disabled="form.processing" class="mt-6"> Save Changes </Button>
        </CardFooter>
      </form>
    </Card>
    <Card class="border-destructive">
      <CardHeader>
        <CardTitle>Danger Zone</CardTitle>
        <CardDescription> Deleting your project is a permanent action. </CardDescription>
      </CardHeader>
      <CardContent class="pt-6">
        <AlertDialog>
          <AlertDialogTrigger as-child>
            <Button variant="destructive">Delete Project</Button>
          </AlertDialogTrigger>
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
              <AlertDialogDescription>
                This action cannot be undone. This will permanently delete this project and all of
                its tasks, members, and activities.
              </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel>Cancel</AlertDialogCancel>
              <AlertDialogAction @click="deleteProject"> Continue & Delete </AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>
      </CardContent>
    </Card>
  </div>
</template>
