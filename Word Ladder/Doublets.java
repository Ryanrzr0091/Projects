import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.Arrays;
import java.util.ArrayDeque;
import java.util.ArrayList;
import java.util.Deque;
import java.util.HashSet;
import java.util.LinkedList;
import java.util.List;
import java.util.Queue;
import java.util.Scanner;
import java.util.Set;
import java.util.TreeSet;
import java.util.stream.Collectors;

/**
 * Provides an implementation of the WordLadderGame interface. 
 *
 * @author Your Name (you@auburn.edu)
 */
public class Doublets implements WordLadderGame {

   private Set<String> lexicon;

   /**
    * Instantiates a new instance of Doublets with the lexicon populated with
    * the strings in the provided InputStream. The InputStream can be formatted
    * in different ways as long as the first string on each line is a word to be
    * stored in the lexicon.
    */
   public Doublets(InputStream in) {
      lexicon = new HashSet<>();
      try {
         Scanner s =
            new Scanner(new BufferedReader(new InputStreamReader(in)));
         while (s.hasNext()) {
            String str = s.next();
            lexicon.add(str.toLowerCase());
            s.nextLine();
         }
         in.close();
      }
      catch (java.io.IOException e) {
         System.err.println("Error reading from InputStream.");
         System.exit(1);
      }
   }
   @Override
   public int getWordCount() {
      return lexicon.size();
   }

   @Override
   public boolean isWord(String str) {
      return lexicon.contains(str.toLowerCase());
   }

   @Override
   public int getHammingDistance(String str1, String str2) {
      if (str1.length() != str2.length()) 
         return -1;
      int distance = 0;
      for (int i = 0; i < str1.length(); i++) {
         if (str1.charAt(i) != str2.charAt(i)) {
            distance++;
         }
      }
      return distance;
   }

   @Override
   public List<String> getNeighbors(String word) {
      List<String> neighbors = new ArrayList<>();
      for (String w : lexicon) {
         if (getHammingDistance(word, w) == 1) {
            neighbors.add(w);
         }
      }
      return neighbors;
   }

   @Override
   public boolean isWordLadder(List<String> sequence) {
      if (sequence == null || sequence.isEmpty()) 
         return false;
      for (int i = 1; i < sequence.size(); i++) {
         if (!isWord(sequence.get(i)) || getHammingDistance(sequence.get(i-1), sequence.get(i)) != 1) {
            return false;
         }
      }
      return true;
   }

   @Override
   public List<String> getMinLadder(String start, String end) {
      if (!isWord(start) || !isWord(end) || start.length() != end.length()) 
         return new ArrayList<>();
   
      Queue<List<String>> queue = new LinkedList<>();
      Set<String> visited = new HashSet<>();
      List<String> startLadder = new ArrayList<>();
      startLadder.add(start);
      queue.add(startLadder);
      visited.add(start);
   
      while (!queue.isEmpty()) {
         List<String> ladder = queue.poll();
         String lastWord = ladder.get(ladder.size() - 1);
         if (lastWord.equals(end)) 
            return ladder;
      
         for (String neighbor : getNeighbors(lastWord)) {
            if (!visited.contains(neighbor)) {
               visited.add(neighbor);
               List<String> newLadder = new ArrayList<>(ladder);
               newLadder.add(neighbor);
               queue.add(newLadder);
            }
         }
      }
      return new ArrayList<>();
   }
}