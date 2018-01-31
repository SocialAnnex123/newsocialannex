<?php

class Socialannex_Satools_Model_System_Config_Source_Dropdown_Values
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'category',
                'label' => 'Category',
            ),
            array(
                'value' => 'brand',
                'label' => 'Brand',
            ),
            array(
                'value' => 'search',
                'label' => 'Search',
            ),
            array(
                'value' => 'college',
                'label' => 'College',
            ),
        );
    }
}