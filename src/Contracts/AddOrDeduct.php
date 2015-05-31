<?php namespace NetworkForGood\Contracts;

/**
 * Indicates how
processing fee
should be
applied to the
transaction.
Required only if
fee coverage is
allowed. Must
also be
configured by
NFG.
 */
interface AddOrDeduct {
	const ADD = 'Add';
	const DEDUCT = 'Deduct';
}