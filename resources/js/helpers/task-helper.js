const TODO = 1;
const IN_PROGRESS = 2;
const COMPLETED = 3;

const URGENT = 4;
const HIGH = 3;
const MEDIUM = 2;
const LOW = 1;

class TaskHelper {
  getStatusLabel(status) {
    switch (status) {
      case TODO:
        return "Completed";
      case IN_PROGRESS:
        return "In Progress";
      default:
        return "To-Do";
    }
  }

  getPriorityLabel(priority) {
    switch (priority) {
      case URGENT:
        return "Urgent";
      case HIGH:
        return "High";
      case MEDIUM:
        return "Medium";
      default:
        return "Low";
    }
  }

  getPriorityVariant(priority) {
    switch (priority) {
      case URGENT:
        return "destructive";
      case HIGH:
        return "destructive";
      case MEDIUM:
        return "secondary";
      default:
        return "outline";
    }
  }
}

export default TaskHelper;
