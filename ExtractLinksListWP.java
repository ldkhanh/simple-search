package CSCI572.HW4;
/* - Khanh HW4 - */
import org.jsoup.*;
import org.jsoup.nodes.*;
import org.jsoup.select.*;

import java.io.*;
import java.util.*;

public class ExtractLinksListWP {
	public static void main(String[] args) throws IOException {
		String location = "/Users/khanh/Code/VBShare/hw4/WP/";
		String csvFile = location + "WP Map.csv";
		BufferedReader br = new BufferedReader(new FileReader(csvFile));
		LinkedHashMap<String,String> map = new LinkedHashMap<String,String>();
		Set<String> edges = new HashSet<String>();
		
		String line = "";
		while((line = br.readLine()) != null){
			String[] mapPair = line.split(",");
			map.put(mapPair[1], mapPair[0]);
		}
		br.close();

		File dir = new File(location+"WP/");
		for(File fileD: dir.listFiles()){
			File file = new File(location+"WP/"+fileD.getName());
			Document doc = Jsoup.parse(file, "UTF-8", "https://www.washingtonpost.com/");
			Elements links = doc.select("a[href]");
		        
		    for(Element link : links){
		        String url = link.attr("abs:href").trim();
		        if(map.containsKey(url)){
		        	edges.add(file.getName() + " " + map.get(url));
		        }
		    }
		}
		try{
		    PrintWriter writer = new PrintWriter(location+"WPedgesList.txt", "UTF-8");
		    for( String s: edges) {
		    		writer.println(s);
		    }
		    writer.flush();
		    writer.close();
		} catch (IOException e) {
		}

	}
}
