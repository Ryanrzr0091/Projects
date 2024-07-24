import java.util.Arrays;
import java.util.Comparator;

/**
 * Binary search.
 */
public class BinarySearch {

   /**
    * Returns the index of the first key in a[] that equals the search key, 
    * or -1 if no such key exists. This method throws a NullPointerException
    * if any parameter is null.
    */
   public static <Key> int firstIndexOf(Key[] a, Key key, Comparator<Key> comparator) {
      if (a == null || key == null || comparator == null) {
         throw new NullPointerException("Input parameters cannot be null");
      }
      int low = 0;
      int high = a.length - 1;
      while (low <= high) {
         int mid = low + (high - low) / 2;
         int cmp = comparator.compare(a[mid], key);
         if (cmp < 0) {
            low = mid + 1;
         } else if (cmp > 0) {
            high = mid - 1;
         } else {
            if (mid == 0 || comparator.compare(a[mid - 1], a[mid]) != 0) {
               return mid;
            } else {
               high = mid - 1;
            }
         }
      }
      return -1;
   }

   /**
    * Returns the index of the last key in a[] that equals the search key, 
    * or -1 if no such key exists. This method throws a NullPointerException
    * if any parameter is null.
    */
   public static <Key> int lastIndexOf(Key[] a, Key key, Comparator<Key> comparator) {
      if (a == null || key == null || comparator == null) {
         throw new NullPointerException("Input parameters cannot be null");
      }
      int low = 0;
      int high = a.length - 1;
      while (low <= high) {
         int mid = low + (high - low) / 2;
         int cmp = comparator.compare(a[mid], key);
         if (cmp < 0) {
            low = mid + 1;
         } else if (cmp > 0) {
            high = mid - 1;
         } else {
            if (mid == a.length - 1 || comparator.compare(a[mid + 1], a[mid]) != 0) {
               return mid;
            } else {
               low = mid + 1;
            }
         }
      }
      return -1;
   }

}
