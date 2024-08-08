<?php

namespace RuleDocGenerator202408\Illuminate\Contracts\Validation;

interface DataAwareRule
{
    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData(array $data);
}
