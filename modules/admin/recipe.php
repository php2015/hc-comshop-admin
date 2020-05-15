<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Recipe_Snailfishshop extends AdminController
{
	public function index()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and recipe_name like :recipe_name';
            $params[':recipe_name'] = '%' . $_GPC['keyword'] . '%';
        }
		
		if( isset($_GPC['state']) && $_GPC['state'] != -1 )
		{
			$condition .= ' and state = '.$_GPC['state'];
		}
		if( isset($_GPC['cate']) && $_GPC['cate'] != '' )
		{
			$condition .= ' and cate_id = '.$_GPC['cate'];
		}
		
		
		$category = load_model_class('goods_category')->getFullCategory(true, true,'recipe');

		

        $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_recipe') . " 
		WHERE uniacid=:uniacid  " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        
		
		foreach( $list as $key => $val )
		{
			
			//cate_id
			//cate_name
			
			$goods_count = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_recipe_ingredients') . 
							' WHERE uniacid=:uniacid and recipe_id=:recipe_id ' ,  array(':recipe_id' => $val['id'],':uniacid' => $_W['uniacid']) );

			$val['username'] = '';
			$val['cate_name'] = '';
			
			if(  $val['member_id'] > 0)
			{
				$mb_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ",
						array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id']));
				if( !empty($mb_info) )
				{
					$val['username'] = $mb_info['username'];
				}
			}
			
			if( $val['cate_id'] > 0 )
			{
				$cate_info = pdo_fetch("select * from ".tablename('lionfish_comshop_goods_category')." where uniacid=:uniacid and id=:id ",
						array(':uniacid' => $_W['uniacid'], ':id' => $val['cate_id'] ));
				if( !empty($cate_info) )
				{
					$val['cate_name'] = $cate_info['name'];
				}
			}
			
			$val['goods_count'] = $goods_count;
			$list[$key] = $val;
		}
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_recipe') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

		include $this->display();
	}

	/**
     * 编辑添加
     */
	public function add()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_recipe') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
			
			//
			
			$ing_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_recipe_ingredients')." where uniacid=:uniacid and recipe_id=:recipe_id ", 
						array(':uniacid' => $_W['uniacid'], ':recipe_id' => $id));
			
			$limit_goods = array();
			
			if( !empty($ing_list) )
			{
				foreach( $ing_list as $key => $val )
				{
					$need_dd = array();
					
					if( !empty($val['goods_id']) )
					{
						$gd_info_list = pdo_fetchall("select id,goodsname from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id in (".$val['goods_id'].") ", 
								array(':uniacid' => $uniacid ));
						
						
						if( !empty($gd_info_list) )
						{
							foreach( $gd_info_list as $gd_info )
							{
								$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . 
									' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', 
									array( ':uniacid' => $_W['uniacid'], ':goods_id' => $gd_info['id'] ));
								$thumb_img =  tomedia($thumb['image']);
								
								$tmp_dd = array();
								$tmp_dd['gid'] = $gd_info['id'];
								$tmp_dd['title'] = $val['title'];
								$tmp_dd['goodsname'] = $gd_info['goodsname'];
								$tmp_dd['image'] = tomedia( $thumb_img );
								
								$need_dd[] = $tmp_dd;
							}
						}
					}
					$val['limit_goods'] = $need_dd;
					
					$ing_list[$key] = $val;
				}
			}
			//limit_goods
			
			//saler
			
			$saler = array();
			
			if( $item['member_id'] > 0 )
			{
				
				$saler = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_member') . ' WHERE member_id = '.$item['member_id'] );
				
			}
			
			
        }
		
		$category = load_model_class('goods_category')->getFullCategory(true, true,'recipe');

        if ($_W['ispost']) {
			
			$need_data = array();
			$need_data['data'] = $_GPC['data'];
			$need_data['sub_name'] = $_GPC['sub_name'];
			$need_data['diff_type'] = $_GPC['diff_type'];
			$need_data['sp'] = $_GPC['sp'];
			$need_data['state'] = $_GPC['state'];
			$need_data['limit_goods_list'] = $_GPC['limit_goods_list'];
			
            load_model_class('recipe')->update($need_data);
            show_json(1, array('url' => referer()));
        }

		include $this->display();
	}

	public function change()
	{
	
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
	
		//ids
		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}

	
		if (empty($id)) {
			show_json(0, array('message' => '参数错误'));
		}
		

		$type = trim($_GPC['type']);
		$value = trim($_GPC['value']);

		if (!(in_array($type, array('state')))) {
			show_json(0, array('message' => '参数错误'));
		}
		
		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_recipe') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_update('lionfish_comshop_recipe', array($type => $value), array('id' => $item['id']));
			
			
		}
		
		show_json(1);
	
	}

	
    public function delete()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_recipe') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        if (empty($item)) {
            $item = array();
        }

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_recipe', array('id' => $item['id']));
            pdo_delete('lionfish_comshop_recipe_ingredients', array('recipe_id' => $item['id']));
            pdo_delete('lionfish_comshop_recipe_fav', array('recipe_id' => $item['id']));
        }

        show_json(1, array('url' => referer()));
    }
	
	public function config()
	{
		
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	
	}
	
	public function slider ()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;
        $params[':type']    = 'recipe';

        $condition = ' and type=:type ';
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and advname like :keyword';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
        }

        if (isset($_GPC['enabled']) && $_GPC['enabled'] >= 0) {
            $_GPC['enabled'] = trim($_GPC['enabled']);
            $condition .= ' and enabled = ' . $_GPC['enabled'];
        } else {
            $_GPC['enabled'] = -1;
        }

        $list = pdo_fetchall('SELECT id,uniacid,advname,thumb,link,type,displayorder,enabled FROM ' . tablename('lionfish_comshop_adv') . "\r\n
		WHERE uniacid=:uniacid   " . $condition . ' order by displayorder desc, id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_adv') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

        include $this->display();
	}
	
	
	public function category()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			if (!empty($_GPC['datas'])) {
				load_model_class('goods_category')->goodscategory_modify();
				show_json(1);
			}
		}
		
		$children = array();
		$category = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' and cate_type="recipe"   ORDER BY pid ASC, sort_order DESC');

		foreach ($category as $index => $row) {
			if (!empty($row['pid'])) {
				$children[$row['pid']][] = $row;
				unset($category[$index]);
			}
		}
		
		include $this->display();
	}
	
	public function addcategory()
	{
		global $_W;
		global $_GPC;
		
		$data = array();
		$pid = isset($_GPC['pid']) ? $_GPC['pid']:0;
		$id = isset($_GPC['id']) ? $_GPC['id']:0;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('goods_category')->update($data,'recipe');
			
			show_json(1, array('shopUrl' => shopUrl('recipe/category')));
		}
		
		if($id >0 )
		{
			$data = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id = \'' . $id . '\'');

		}
		
		include $this->display();
	}
	public function category_delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT id, name, pid FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id = \'' . $id . '\'');

		if (empty($item)) {
			$this->message('抱歉，分类不存在或是已经被删除！', shopUrl('recipe/category'), 'error');
		}
		pdo_delete('lionfish_comshop_goods_category', array('id' => $id, 'pid' => $id), 'OR');
		
		show_json(1, array('url' => referer()));
	}
	
	public function category_enabled()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_goods_category', array('is_show' => intval($_GPC['enabled'])), array('id' => $item['id']));
			
		}
		show_json(1, array('url' => referer()));
	}
	
	 public function addslider()
    {
        global $_W;
        global $_GPC;

        $uniacid = $_W['uniacid'];

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_adv') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));

        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            load_model_class('adv')->update($data,'recipe');
            show_json(1, shopUrl('recipe.slider') );
        }

        include $this->display();
    }
	public function changeslider()
    {

        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        //ids
        if (empty($id)) {
            $id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
        }

        if (empty($id)) {
            show_json(0, array('message' => '参数错误'));
        }

        $type  = trim($_GPC['type']);
        $value = trim($_GPC['value']);

        if (!(in_array($type, array('enabled', 'displayorder')))) {
            show_json(0, array('message' => '参数错误'));
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_adv') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_update('lionfish_comshop_adv', array($type => $value), array('id' => $item['id']));
        }

        show_json(1);

    }

    public function deleteslider()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        //ids
        if (empty($id)) {
            $id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
        }

        if (empty($id)) {
            show_json(0, array('message' => '参数错误'));
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_adv') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_adv', array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
        }

        show_json(1);
    }
	
	public function order()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and order_sn like :order_sn';
            $params[':order_sn'] = '%' . $_GPC['keyword'] . '%';
        }
        
		
		$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_card_order') . " 
		WHERE uniacid=:uniacid and state= 1  " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	    
		if( !empty($list) )
		{
			foreach( $list  as  $key => $val )
			{
				//member_id
				
				$mb_info = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_member') . ' WHERE member_id = '.$val['member_id'] );
				
				$val['username'] = $mb_info['username'];
				$list[$key] = $val;
			}
		}
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member_card_order') . ' WHERE uniacid=:uniacid and state= 1  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

		include $this->display();
	}
	

}