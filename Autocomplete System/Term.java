import java.util.Comparator;

/**
 * Autocomplete term representing a (query, weight) pair.
 * 
 */
public class Term implements Comparable<Term> {
   private final String query;
   private final long weight;

   /**
    * Initialize a term with the given query and weight.
    * This method throws a NullPointerException if query is null,
    * and an IllegalArgumentException if weight is negative.
    */
   public Term(String query, long weight) {
      if (query == null) {
         throw new NullPointerException("Query cannot be empty");
      }
      if (weight < 0) {
         throw new IllegalArgumentException("Weight cannot be negative");
      }
      this.query = query;
      this.weight = weight;
   }

   public String getQuery() {
      return query;
   }

   public long getWeight() {
      return weight;
   }

   /**
    * Compares the two terms in descending order of weight.
    */
   public static Comparator<Term> byDescendingWeightOrder() {
      return Comparator.comparingLong(Term::getWeight).reversed();
   }

   /**
    * Compares the two terms in ascending lexicographic order of query,
    * but using only the first length characters of query. This method
    * throws an IllegalArgumentException if length is less than or equal
    * to zero.
    */
   public static Comparator<Term> byPrefixOrder(int length) {
      if (length <= 0) {
         throw new IllegalArgumentException("Length must be greater than zero");
      }
      return Comparator.comparing((Term t) -> t.getQuery().substring(0, Math.min(length, t.getQuery().length())));
   }

   /**
    * Compares this term with the other term in ascending lexicographic order
    * of query.
    */
   @Override
   public int compareTo(Term other) {
      return this.query.compareTo(other.query);
   }

   /**
    * Returns a string representation of this term in the following format:
    * query followed by a tab followed by weight
    */
   @Override
   public String toString(){
      return query + "\t" + weight;
   }

}

