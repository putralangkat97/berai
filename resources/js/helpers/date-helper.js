import { format, parseISO } from "date-fns";

class DateHelper {
  formatDate(dateString) {
    if (!dateString) return "N/A";
    try {
      return format(parseISO(dateString), "MMM d");
    } catch (e) {
      return dateString;
    }
  }
}

export default DateHelper;
