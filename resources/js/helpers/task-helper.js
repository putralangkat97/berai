class TaskHelper {
  getStatusLabel(status) {
    switch (status) {
      case 3:
        return "Completed";
      case 2:
        return "In Progress";
      default:
        return "To-Do";
    }
  }

  getPriorityLabel(priority) {
    switch (priority) {
      case 4:
        return "Urgent";
      case 3:
        return "High";
      case 2:
        return "Medium";
      default:
        return "Low";
    }
  }

  getPriorityVariant(priority) {
    switch (priority) {
      case 4:
        return "destructive";
      case 3:
        return "destructive";
      case 2:
        return "secondary";
      default:
        return "outline";
    }
  }
}

export default TaskHelper;
