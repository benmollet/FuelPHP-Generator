<div class="panel panel-menu">
    <div class="panel-body">
        <?php
        foreach ($menuArray as $menuItem)
        {
            if (is_array($menuItem) === false)
            {
                throw new \Exception('The menu must be an array');
                return;
            }
            if (key_exists('location', $menuItem) === false)
            {
                throw new \Exception('No link location specified');
                return;
            }
            
            if (key_exists('text', $menuItem) === false)
            {
                throw new \Exception('No link text specified');
                return;
            }
            
            echo '<a href="';
            echo \Uri::create($menuItem['location']);
            echo '"';
            
            if (isset($menuItem['class']) === true)
            {
                echo ' class="';
                echo $menuItem['class'];
                echo '"';
            }
            
            echo ' role="button">';
            echo $menuItem['text'];
            echo '</a>';
            echo "\r\n";
            
            //$menuHtml .= \View::forge('generate/menu', $menuItem);
            
            //$menuHtml .= '<a href="'.$menuItem['location'].'"';
            //
            //if (key_exists('class', $menuItem) === true)
            //{
            //    $menuHtml .= ' class="'.$menuItem['class'].'"';
            //}
            //
            //$menuHtml .= '>';
            //
            //$menuHtml .= $menuItem['text'];
            //
            //$menuHtml .= '</a>';
        }
        ?>
    </div>
</div>