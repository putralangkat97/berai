import { format, formatDistanceToNow, parseISO } from "date-fns";

class DateHelper {
  formatDate(dateString, dateFormat = "MMM d") {
    if (!dateString) return "N/A";
    try {
      return format(parseISO(dateString), dateFormat);
    } catch (e) {
      return dateString;
    }
  }

  relativeTime(dateString, suffix = true) {
    if (!dateString) return "N/A";
    try {
      return formatDistanceToNow(parseISO(dateString), { addSuffix: suffix });
    } catch (e) {
      return dateString;
    }
  }
}

export default DateHelper;
