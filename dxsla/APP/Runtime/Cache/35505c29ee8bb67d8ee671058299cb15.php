<?php if (!defined('THINK_PATH')) exit(); if(is_array($answerlist)): $i = 0; $__LIST__ = $answerlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$a): $mod = ($i % 2 );++$i;?><dd id="dl_<?php echo ($a["id"]); ?>">
  <div class="clear">
    <div class="text">
      <div class="pep_img"><a href="<?php echo ($urldomain); ?>/<?php echo ($a["uid"]); ?>" target="_blank"><img src="<?php echo ($urlupload); ?>/<?php echo ($a["default_pic"]); ?>_48x48.jpg" width="32" height="32"/></a></div>
      <a href="<?php echo ($urldomain); ?>/<?php echo ($a["uid"]); ?>" target="_blank" class="f_pink fl word_nowrap"><?php echo ($a["username"]); ?>：</a><a class="<?php if(($a['vote']) == "1"): ?>agree<?php else: ?>opposition<?php endif; ?> fl"></a>
      <p class="word_break"><?php echo ($a["answer_cont"]); ?></p>
    </div>
    <div class="remark_info fs_12"><span class="f_9"><?php echo (formattime($a["answer_time"])); ?></span>
	  <?php if(($a['star_num']) > "0"): $num = $a['star_num'] > 3 ? $a['star_num'] - 3 : $a['star_num']; ?>
	  <p class="fr f_6">已给<?php if(($a['star_num']) > "3"): ?>差<?php else: ?>好<?php endif; ?>评：<span class="<?php if(($a['star_num']) > "3"): ?>sp_defecate<?php else: ?>sp_star<?php endif; ?> bg_postion<?php echo ($num); ?>"></span></p>
	  <?php else: ?>
      <p class="fr f_6" id="tip_<?php echo ($a["id"]); ?>_<?php echo ($a["uid"]); ?>">给<?php echo (ui_sex($a['sex'])); ?>好评：<span id="set_<?php echo ($a["id"]); ?>"><a class="sp_star bg_postion setstar " data="18090226|294|<?php echo ($a["id"]); ?>|<?php echo ($a["uid"]); ?>|10829" id="setstar_<?php echo ($a["id"]); ?>" onclick="ping_star('setstar_<?php echo ($a["id"]); ?>')" onmouseout="setstar_id = 0;this.className = 'sp_star bg_postion setstar';" onmousemove="setstar_id='<?php echo ($a["id"]); ?>';DisplayCoord(event,0);"></a></span></p>
      <p class="fr f_6" id="tippoor_<?php echo ($a["id"]); ?>_<?php echo ($a["uid"]); ?>">给<?php echo (ui_sex($a['sex'])); ?>差评：<span id="set_<?php echo ($a["id"]); ?>"><a class="sp_defecate bg_postion setstar " data="18090226|294|<?php echo ($a["id"]); ?>|<?php echo ($a["uid"]); ?>|10829" id="setpoor_<?php echo ($a["id"]); ?>" onclick="ping_star('setpoor_<?php echo ($a["id"]); ?>')" onmouseout="setpoor_id = 0;this.className = 'sp_defecate bg_postion setstar';" onmousemove="setpoor_id='<?php echo ($a["id"]); ?>';DisplayCoord(event,1);"></a></span></p><?php endif; ?>
	  
      <p><a class="f_bl" href="javascript:;" onclick="reply(<?php echo ($a["id"]); ?>,<?php echo ($a["uid"]); ?>,<?php echo ($a["id"]); ?>)">回复</a> | <a class="f_6 delete_answer" onclick="delete_confirm(<?php echo ($a["id"]); ?>,'answer',1,294)">删除</a></p>
    </div>
  </div>
  <div class="clear comment" id="comment_<?php echo ($a["id"]); ?>"></div>
  <div class="clear border"></div>
</dd><?php endforeach; endif; else: echo "" ;endif; ?>