function get_left_menu($arr_perishable)
{
    $result = cache::get('left_menu');
    if($result === false)
    {
        $sort_func = function($a, $b){
                if($a['ord'] == $b['ord'])
                {
                    $a_c = iconv('utf8', 'cp1251', $a['name']);
                    $b_c = iconv('utf8', 'cp1251', $b['name']);
                    if(ord($a_c[0]) == ord($b_c[0]))
                        return 0;
                    return ord($a_c[0]) < ord($b_c[0]) ? -1 : 1;
                }
                return ($a['ord'] < $b['ord'] ? -1 : 1);
        };
        $category_hierarchy_list = get_category_products_hierarchy();
        usort($category_hierarchy_list, $sort_func);

        $result = [];
        $hierarchy_to_list = function($cat) use (&$hierarchy_to_list, &$result, $sort_func, $arr_perishable){
            usort($cat, $sort_func);
            foreach($cat as $c)
            {
                $c['has_children'] = false;
                $c['perishable_count'] = ( isset($arr_perishable[$c['id']]) ? $arr_perishable[$c['id']] : 0);
                if($c['sub_section']){
                    $c['has_children'] = true;
                    $result[] = $c;
                    $hierarchy_to_list($c['sub_section']);
                }
                else 
                    $result[] = $c;
            }
        };
        foreach($category_hierarchy_list as $cat)
        {
            $cat['has_children'] = false;
            $cat['perishable_count'] = ( isset($arr_perishable[$cat['id']]) ? $arr_perishable[$cat['id']] : 0);
            if($cat['sub_section']){
                $cat['has_children'] = true;
                $result[] = $cat;        
                $hierarchy_to_list($cat['sub_section']);
            }
            else 
                $result[] = $cat;
        }
        cache::set('left_menu', $result);
    }
    return $result;
}
