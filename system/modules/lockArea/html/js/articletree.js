
/**
 * Class AjaxRequest
 *
 * Provide methods to handle Ajax requests.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Backend
 */
var AjaxArticleTreeRequest =
{


  /**
	 * Toggle the page tree input field
	 * @param object
	 * @param string
	 * @param string
	 * @param string
	 * @param integer
	 * @return boolean
	 */
  togglePagetree: function (el, id, field, name, level) {
    el.blur();
    var item = $(id);
    var image = $(el).getFirst('img');

    if (item) {
      if (item.getStyle('display') == 'none') {
        item.setStyle('display', 'inline');
        image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
        $(el).title = CONTAO_COLLAPSE;
        new Request.Contao({
          field:el
        }).post({
          'action':'toggleArticleTree', 
          'id':id, 
          'state':1, 
          'REQUEST_TOKEN':REQUEST_TOKEN
        });
      } else {
        item.setStyle('display', 'none');
        image.src = image.src.replace('folMinus.gif', 'folPlus.gif');
        $(el).title = CONTAO_EXPAND;
        new Request.Contao({
          field:el
        }).post({
          'action':'toggleArticleTree', 
          'id':id, 
          'state':0, 
          'REQUEST_TOKEN':REQUEST_TOKEN
        });
      }
      return false;
    }

    new Request.Contao({
      field: el,
      evalScripts: true,
      onRequest: AjaxRequest.displayBox(CONTAO_LOADING + ' …'),
      onSuccess: function(txt, json) {
        var li = new Element('li', {
          'id': id,
          'class': 'parent',
          'styles': {
            'display': 'inline'
          }
        });

        var ul = new Element('ul', {
          'class': 'level_' + level,
          'html': txt
        }).inject(li, 'bottom');
                
        // Search articles
        var insertElement = $(el).getParent('li');        
                
        for(var i = 0 ; i < 100; i++)
        {          
          var nextInsertElement = insertElement.getNext('li');
          
          if(nextInsertElement == null || !nextInsertElement.hasClass('tl_article'))
          {
            break;
          } 
          else
          {
            insertElement = nextInsertElement;
          }
        }

        li.inject(insertElement, 'after');
        $(el).title = CONTAO_COLLAPSE;
        image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
        AjaxRequest.hideBox();

        // HOOK
        window.fireEvent('ajax_change');
      }
    }).post({
      'action':'loadArticleTree', 
      'id':id, 
      'level':level, 
      'field':field, 
      'name':name, 
      'state':1, 
      'REQUEST_TOKEN':REQUEST_TOKEN
    });

    return false;
  }
};
