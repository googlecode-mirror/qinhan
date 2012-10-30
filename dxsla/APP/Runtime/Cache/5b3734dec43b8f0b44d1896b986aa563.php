<?php if (!defined('THINK_PATH')) exit();?><div id="attention_more" class="myfrend_new">
<?php foreach($feedlist as $f) { ?>
<div class="item clear">
  <div class="item_t"> <a target="_blank" href="<?php echo ($urldomain); ?>/<?php echo ($f['uid']); ?>"> <img class="face_s<?php echo ($f['sex']); ?>_b2" src="<?php echo ($urlupload); echo ($f['default_pic']); ?>_<?php echo ($face_size); ?>x<?php echo ($face_size); ?>.jpg"> </a> </div>
  <ul class="item_c">
    <?php foreach($f['data'] as $d) { ?>
	<?php $body = unserialize($d['body']) ?>
    <li class="<?php if($d['type'] == 1) echo 'line'; else echo 'last' ?>">
      <dl class="clear">
        <dt>
          <ul>            
            <?php if($d['type'] == 1) { ?>
			<li class="f_6"><a target="_blank" href="<?php echo ($urldomain); ?>/<?php echo ($f['uid']); ?>"><?php echo ($f['username']); ?></a> ，<a target="_blank" href="<?php echo ($urlsite); ?>/diary" class="f_6 underline">写了两句</a></li>
            <li>
              <p class="word_break"><?php echo ($body['content']); ?>&nbsp;&nbsp;<span id="praise_<?php echo ($body["did"]); ?>" class="word_nowrap"><a title="给<?php echo (ui_sex($f['sex'])); echo (ui_sex($f['sex'],9)); ?>" class="<?php echo (ui_sex($f['sex'],8)); ?>_zbtn" onclick="praise_diary(<?php echo ($f['uid']); ?>,<?php echo ($body["did"]); ?>,<?php echo ($f['sex']); ?>,0)">&nbsp;&nbsp;</a><a class="brick" title="给<?php echo (ui_sex($f['sex'])); ?>一板砖" onclick="praise_diary(<?php echo ($f['uid']); ?>,<?php echo ($body["did"]); ?>,<?php echo ($f['sex']); ?>,1)"></a></span>&nbsp;&nbsp;<a class="word_nowrap dashed" onclick="show_comment_form(<?php echo ($f['uid']); ?>,<?php echo ($body["did"]); ?>,16);">评论</a></p>
              <div id="comment_<?php echo ($f['uid']); ?>_<?php echo ($body["did"]); ?>" class="reply_box" style="display:none" ></div>
            </li>
			
            <?php } elseif($d['type'] == 2) { ?>
			<li class="f_6"><a target="_blank" href="<?php echo ($urldomain); ?>/<?php echo ($f['uid']); ?>"><?php echo ($f['username']); ?></a> ，更新了我想！</li>
            <li><span class="fb_13">“</span><span>想和一个<?php echo (ui_sex($f['sex'],3)); ?>生,<?php echo ($body["want_content"]); ?></span><span class="fb_13 f_0">”</span> <a onclick="try_meet(<?php echo ($f["uid"]); ?>,<?php echo ($body["sex"]); ?>,'<?php echo ($f["username"]); ?>','<?php echo ($body["want_content"]); ?>')" class="f_r underline">约<?php echo (ui_sex($f['sex'])); ?></a></li>
			
            <?php } elseif($d['type'] == 3) { ?>
			<li class="f_6"><a target="_blank" href="<?php echo ($urldomain); ?>/<?php echo ($f['uid']); ?>"><?php echo ($f['username']); ?></a> ，提了<span class="f_yelo"><?php echo ($d['count']); ?></span>个问题！</li>
            <?php foreach($body as $b) { ?>
            <li class="m_t10">
              <p class="m_t15 word_break"><span class="f_green word_nowrap">问题：</span><?php echo ($b['question']); ?>&nbsp; <a id="<?php echo ($b["q_id"]); ?>" class="f_blue fs_12 answer_question font_block" onclick="show_answer_form(this);">回答</a>&nbsp;<a target="_blank" style="display:none;" href="<?php echo ($urlsite); ?>/home/question/?uid=<?php echo ($b["q_uid"]); ?>&amp;qid=<?php echo ($b["q_id"]); ?>" class="dashed word_nowrap font_block" id="answer_more_<?php echo ($b["q_id"]); ?>">查看全部答案</a></p>
			  <?php if(!empty($b['photo_url'])): ?><p class="m_t5"><img title="点击查看大图" class="mousezoom_tip" src="<?php echo ($urlupload); ?>/<?php echo ($b['photo_url']); ?>_120x120.jpg" onclick="scaleImg2('<?php echo ($b["q_uid"]); ?>_<?php echo ($b["q_id"]); ?>','<?php echo ($urlupload); ?>/<?php echo ($b['photo_url']); ?>_480x480.jpg')" id="imgsmall2_<?php echo ($b["q_uid"]); ?>_<?php echo ($b["q_id"]); ?>"> <img onclick="scaleImg2('<?php echo ($b["q_uid"]); ?>_<?php echo ($b["q_id"]); ?>','')" class="mousezoom_min" style="display:none;" id="imgbig2_<?php echo ($b["q_uid"]); ?>_<?php echo ($b["q_id"]); ?>"></p><?php endif; ?>
            </li>
            <div style="display:none" class="myfrend_reply" data="<?php echo ($b["q_uid"]); ?>" id="answer_<?php echo ($b["q_id"]); ?>"></div>			
            <?php } ?>
			
            <?php } elseif($d['type'] == 4) { ?>
			<li class="f_6"><a target="_blank" href="<?php echo ($urldomain); ?>/<?php echo ($f['uid']); ?>"><?php echo ($f['username']); ?></a> ，回答了<span class="f_yelo"><?php echo ($d['count']); ?></span>个问题！</li>
            <?php foreach($body as $b) { ?>
            <li class="m_t10">
              <p class="m_t15 word_break"><span class="f_green word_nowrap">问题：</span><?php echo ($b['question']); ?>&nbsp; <a title="我也要回答这个问题" id="<?php echo ($b["q_id"]); ?>_<?php echo ($b["q_uid"]); ?>" class="f_r fs_12 answer_question dashed word_nowrap" onclick="show_quesiton_answer_form_div(this);">回答</a></p>
			  <?php if(!empty($b['photo_url'])): ?><p class="m_t5"><img title="点击查看大图" class="mousezoom_tip" src="<?php echo ($urlupload); echo ($b['photo_url']); ?>_120x120.jpg" onclick="scaleImg2('<?php echo ($b["q_uid"]); ?>_<?php echo ($b["q_id"]); ?>','<?php echo ($urlupload); ?>/<?php echo ($b['photo_url']); ?>_480x480.jpg')" id="imgsmall2_<?php echo ($b["q_uid"]); ?>_<?php echo ($b["q_id"]); ?>"> <img onclick="scaleImg2('<?php echo ($b["q_uid"]); ?>_<?php echo ($b["q_id"]); ?>','')" class="mousezoom_min" style="display:none;" id="imgbig2_<?php echo ($b["q_uid"]); ?>_<?php echo ($b["q_id"]); ?>"></p><?php endif; ?>
              <div style="display:none" class="myfrend_reply" data="<?php echo ($b["q_uid"]); ?>" id="answer_<?php echo ($b["q_id"]); ?>_<?php echo ($b["q_uid"]); ?>"></div>
              <div class="m_t5 clear"><span class="f_blue2 fl word_nowrap"><a href="<?php echo ($urldomain); ?>/<?php echo ($b["a_uid"]); ?>" target="_blank"><?php echo ($b["a_username"]); ?></a>：</span><span class="agree   fl"></span>
                <p class="f_6 word_break"><?php echo ($b['answer']); ?>&nbsp; <a title="评论<?php echo (ui_sex($f['sex'])); ?>的回答" onclick="show_comment_form(<?php echo ($b["a_uid"]); ?>,<?php echo ($b["a_id"]); ?>,10);" class="dashed word_nowrap">评论</a></p>
              </div>
            </li>
            <div style="display:none" class="reply_box" id="comment_<?php echo ($b["a_uid"]); ?>_<?php echo ($b["a_id"]); ?>"></div>
            <?php } ?>
			
            <?php } elseif($d['type'] == 5) { ?>
			<li class="f_6"><a target="_blank" href="<?php echo ($urldomain); ?>/<?php echo ($f['uid']); ?>"><?php echo ($f['username']); ?></a> ，上传了<span class="f_yelo"><?php echo ($d['count']); ?></span>张照片！</li>
            <li id="photo_10347661_1324280879" class="m_t10">
              <?php foreach($body as $b) { ?>
              <span class="p5"><a target="_blank" href="<?php echo ($urlsite); ?>/home/photo?uid=<?php echo ($f['uid']); ?>&amp;gid=<?php echo ($b['gid']); ?>&amp;pid=<?php echo ($b['pid']); ?>"><img border="0" src="<?php echo ($urlupload); ?>/<?php echo ($b['path']); ?>_999x80.jpg"></a></span>
              <?php } ?>
            </li>
            <!--<li class="m_t10"><a onclick="load_attention('photo','10347661','1781941,1781942','1324280879',this)" style="text-decoration:underline;">查看更多&gt;&gt;</a></li>-->
            <?php } elseif($d['type'] == 6) { ?>
			<li class="f_6"><a target="_blank" href="<?php echo ($urldomain); ?>/<?php echo ($f['uid']); ?>"><?php echo ($f['username']); ?></a> ，更新了<span class="f_yelo"><?php echo ($d['count']); ?></span>条小编专访！</li>
            <?php foreach($body as $b) { ?>
            <li class="m_t10">
              <p><span class="f_green">小编：</span><?php echo ($b['wenwen_question']); ?></p>
              <p><span class="f_blue2">回答：</span><span class="f_6"><?php echo ($b['wenwen_answer']); ?>&nbsp; <a onclick="show_comment_form(<?php echo ($f['uid']); ?>,<?php echo ($b['id']); ?>,4);" class="dashed word_nowrap">评论</a></span></p>
            </li>
            <div style="display:none" class="reply_box" id="comment_<?php echo ($f['uid']); ?>_<?php echo ($b['id']); ?>"></div>
            <?php } ?>
			
            <?php } elseif($d['type'] == 21) { ?>
			<li class="f_6"><a target="_blank" href="<?php echo ($urldomain); ?>/<?php echo ($f['uid']); ?>"><?php echo ($f['username']); ?></a> ，发布了<span class="f_yelo"><?php echo ($d['count']); ?></span>个校园任务！</li>
            <?php foreach($body as $b) { ?>
            <li class="m_t10">
              <p class="m_t15 word_break"><span class="f_green word_nowrap">任务：</span><?php echo ($b['title']); ?>&nbsp; <a target="_blank" href="<?php echo ($urlsite); ?>/home/task/?uid=<?php echo ($f["uid"]); ?>&amp;tid=<?php echo ($b["tid"]); ?>" class="dashed word_nowrap font_block">详情</a></p>

            </li>
            <div style="display:none" class="myfrend_reply" data="<?php echo ($b["q_uid"]); ?>" id="answer_<?php echo ($b["q_id"]); ?>"></div>			
            <?php } ?>
			<?php } ?>	
          </ul>
        </dt>
        <dd> <span class="fs_12 f_9"><?php echo (formattime($d['add_time'])); ?></span> </dd>
      </dl>
    </li>
    <?php } ?>
  </ul>
</div>
<?php } ?>

</div>
<?php $js_act = ACTION_NAME == 'attention' ? 'load_index' : 'load_more'; ?>
<p class="list" id="load_more_dt"><a href="javascript:void(0)" onclick="<?php echo ($js_act); ?>();"><img src="<?php echo ($urlstatic); ?>/img/morebar.png" width="99" height="25" /></a></p>
<script>
var answers = null;
var is_last = 0;
var last_id = 1518137;
var limit = 1;
var face_size = 48;
</script>
<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/pub_face_all.js"></script>
<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/attention.js"></script>
<script>
$(document).ready(function() {
        if(Cookies.get("locat_pay_card") == 1) {
            show_mask_info(2,0);
            Cookies.clear("locat_pay_card");
            Cookies.clear("locat_pay_uid");
        }
});
</script>