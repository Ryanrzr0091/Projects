import java.util.Arrays;


/**
 * Autocomplete.
 */
public class Autocomplete {

   private Term[] terms;

	/**
	 * Initializes a data structure from the given array of terms.
	 * This method throws a NullPointerException if terms is null.
	 */
   public Autocomplete(Term[] terms) {
      if (terms == null) {
         throw new NullPointerException("Terms cannot be null");
      }
      this.terms = Arrays.copyOf(terms, terms.length);
      Arrays.sort(this.terms);
   }

	/** 
	 * Returns all terms that start with the given prefix, in descending order of weight. 
	 * This method throws a NullPointerException if prefix is null.
	 */
   public Term[] allMatches(String prefix) {
      if (prefix == null) {
         throw new NullPointerException("Prefix cannot be null");
      }
        
      Term key = new Term(prefix, 0); // Dummy Term with weight 0 to use for searching
      int firstIndex = BinarySearch.firstIndexOf(terms, key, Term.byPrefixOrder(prefix.length()));
      if (firstIndex == -1) {
         return new Term[0]; // No matches found
      }
        
      int lastIndex = BinarySearch.lastIndexOf(terms, key, Term.byPrefixOrder(prefix.length()));
        
        // Copy the matched terms into a new array
      int numMatches = lastIndex - firstIndex + 1;
      Term[] matches = new Term[numMatches];
      System.arraycopy(terms, firstIndex, matches, 0, numMatches);
        
        // Sort the matches in descending order of weight
      Arrays.sort(matches, Term.byDescendingWeightOrder());
        
      return matches;
   }

}

