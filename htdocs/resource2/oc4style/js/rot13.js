/*                                                                              
                                                                                    
    rot13.js.  Version 1.3.                                                         
                                                                                    
   Rot13 functions by Valentin Hilbig, who has placed them in the public domain.   
    <http://tools.geht.net/rot13.html>.                                             
                                                                                    
    Improvements have been made by Ewan Mellor <rot13@ewanmellor.org.uk>, who has
    also placed his code in the public domain.                                      
                                                                                  
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR      
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,        
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE     
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER          
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,   
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE   
    SOFTWARE.                                                                       
                                                                                    
                                                                                    
  This file may be obtained from <http://www.ewanmellor.org.uk/javascript/>.      
                                                                                    
    */                                                                              
                                                                                    
                                                                                    
  /**                                                                             
     * @return The rot13 of the given string.  The &#nn;, &lt;, &gt;, and &amp;     
     * entities are also expanded, as entities are sometimes left unprocessed by    
     * browsers when performing Element.innerText or                                
     * Element.childNodes[0].nodeValue.  Other characters not in [A-Za-z] are       
   * placed in the output without change.                                         
     */                                                                             
    function rot13(a)                                                               
    {                                                                               
      if (!rot13map)                                                                
    {                                                                             
        rot13map = rot13init();                                                     
      }                                                                             
                                                                                    
      var s = "";                                                                   
    var i;                                                                        
      var entity = "";                                                              
      for (i = 0; i < a.length; i++)                                                
      {                                                                             
        var b = a.charAt(i);                                                        
                                                                                  
        if (entity)                                                                 
        {                                                                           
          entity += b;                                                              
                                                                                    
        if (b == ';')                                                             
          {                                                                         
            if (entity == "&lt;")                                                   
            {                                                                       
              s += "<";                                                             
          }                                                                       
            else if (entity == "&gt;")                                              
            {                                                                       
              s += ">";                                                             
            }                                                                       
          else if (entity == "&amp;")                                             
            {                                                                       
              s += "&";                                                             
            }                                                                       
            else                                                                    
          {                                                                       
              var matches = entity.match(rot13numericEntityRE);                     
                                                                                    
              if (matches[1])                                                       
              {                                                                     
              s+= String.fromCharCode(matches[1]);                                
              }                                                                     
            }                                                                       
                                                                                    
            entity = "";                                                            
        }                                                                         
        }                                                                           
        else if (b == '&')                                                          
        {                                                                           
          entity = "&";                                                             
      }                                                                           
        else                                                                        
        {                                                                           
          s += (b >= 'A' && b <= 'Z' || b >= 'a' && b <= 'z' ? rot13map[b] : b);    
        }                                                                           
    }                                                                             
                                                                                    
      return s;                                                                     
    }                                                                               
                                                                                    
                                                                                  
    rot13map = null;                                                                
    rot13numericEntityRE = /&#(.*);/;                                               
                                                                                    
                                                                                    
  /**                                                                             
     * Initialise this module.  This is called by rot13 if necessary -- there is    
     * no need to call it explicitly yourself.                                      
     */                                                                             
    function rot13init()                                                            
 {                                                                               
      var map = new Array();                                                        
      var s   = "abcdefghijklmnopqrstuvwxyz";                                       
                                                                                    
      var i;                                                                        
   for (i = 0; i < s.length; i++)                                                
      {                                                                             
        map[s.charAt(i)]               = s.charAt((i + 13) % 26);                   
      }                                                                             
      for (i = 0; i < s.length; i++)                                                
   {                                                                             
        map[s.charAt(i).toUpperCase()] = s.charAt((i + 13) % 26).toUpperCase();     
      }                                                                             
                                                                                    
      return map;                                                                   
 }